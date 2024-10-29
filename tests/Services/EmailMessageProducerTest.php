<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Tests\Services;

use JokerVDen\EmailKafkaSender\Collections\AttachmentCollection;
use JokerVDen\EmailKafkaSender\Contracts\AttachmentStorageInterface;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDataDto;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use JokerVDen\EmailKafkaSender\DTOs\EmailRequestDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;
use JokerVDen\EmailKafkaSender\Providers\EmailKafkaSenderServiceProvider;
use JokerVDen\EmailKafkaSender\Services\EmailMessageProducer;
use JokerVDen\EmailKafkaSender\Tests\Enums\TestSource;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EmailMessageProducerTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('kafka.brokers', 'localhost:9092');
        $app['config']->set('email-kafka-sender.topic', 'email-topic');
    }

    protected function getPackageProviders($app): array
    {
        return [EmailKafkaSenderServiceProvider::class];
    }

    #[Test]
    public function email_message_is_sent_to_kafka(): void
    {
        Kafka::fake();

        $attachmentStorageMock = $this->createMock(AttachmentStorageInterface::class);

        $attachmentDto = $this->createMock(AttachmentDto::class);
        $attachmentDataDto = new AttachmentDataDto('somefile.txt', 'test.te');

        $attachmentStorageMock
            ->method('storeAttachment')
            ->willReturn($attachmentDataDto);

        $producer = new EmailMessageProducer(
            $attachmentStorageMock,
            config('email-kafka-sender.topic'),
            TestSource::TEST_SOURCE,
        );

        $attachments = new AttachmentCollection([$attachmentDto]);
        $requestDto = new EmailRequestDto(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            'Test Body',
            $attachments
        );

        $producer->sendEmailMessage($requestDto);

        Kafka::assertPublishedOn(
            topic: config('email-kafka-sender.topic'),
            callback: static function (Message $message) {
                $messageData = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);

                $expectedData = [
                    'source' => 'test_source',
                    'from' => 'sender@example.com',
                    'to' => 'recipient@example.com',
                    'subject' => 'Test Subject',
                    'body' => 'Test Body',
                    'attachments' => [
                        [
                            'fileName' => 'somefile.txt',
                            'url' => 'test.te',
                        ],
                    ],
                    'event_id' => $messageData['event_id'],
                ];

                return $messageData === $expectedData;
            });
    }

    #[Test]
    public function email_message_fails_when_attachment_storage_fails(): void
    {
        $this->expectException(AttachmentStorageFailedException::class);

        $attachmentStorageMock = $this->createMock(AttachmentStorageInterface::class);
        $attachmentDto = $this->createMock(AttachmentDto::class);

        $attachmentStorageMock
            ->method('storeAttachment')
            ->willThrowException(new AttachmentStorageFailedException('filename.txt'));

        $producer = new EmailMessageProducer(
            $attachmentStorageMock,
            config('email-kafka-sender.topic'),
            TestSource::TEST_SOURCE,
        );

        $attachments = new AttachmentCollection([$attachmentDto]);
        $requestDto = new EmailRequestDto(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            'Test Body',
            $attachments
        );

        $producer->sendEmailMessage($requestDto);
    }

    #[Test]
    public function email_message_is_sent_to_kafka_without_attachments(): void
    {
        Kafka::fake();

        $attachmentStorageMock = $this->createMock(AttachmentStorageInterface::class);

        $producer = new EmailMessageProducer(
            $attachmentStorageMock,
            config('email-kafka-sender.topic'),
            TestSource::TEST_SOURCE,
        );

        $requestDto = new EmailRequestDto(
            'sender@example.com',
            'recipient@example.com',
            'Test Subject',
            'Test Body',
            null, // attachments is null
        );

        $producer->sendEmailMessage($requestDto);

        Kafka::assertPublishedOn(
            topic: config('email-kafka-sender.topic'),
            callback: function (Message $message) {
                $messageData = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);

                $expectedData = [
                    'source' => 'test_source',
                    'from' => 'sender@example.com',
                    'to' => 'recipient@example.com',
                    'subject' => 'Test Subject',
                    'body' => 'Test Body',
                    'attachments' => [], // We expect that there are no attachments
                    'event_id' => $messageData['event_id'],
                ];

                return $messageData === $expectedData;
            }
        );
    }
}
