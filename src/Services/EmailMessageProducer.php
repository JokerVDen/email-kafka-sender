<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Services;

use Exception;
use JokerVDen\EmailKafkaSender\Collections\AttachmentCollection;
use JokerVDen\EmailKafkaSender\Contracts\AttachmentStorageInterface;
use JokerVDen\EmailKafkaSender\Contracts\EmailMessageProducerInterface;
use JokerVDen\EmailKafkaSender\Contracts\SourceContract;
use JokerVDen\EmailKafkaSender\DTOs\EmailMessageDto;
use JokerVDen\EmailKafkaSender\DTOs\EmailRequestDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;
use JsonException;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Ramsey\Uuid\Uuid;

class EmailMessageProducer implements EmailMessageProducerInterface
{
    public function __construct(
        private readonly AttachmentStorageInterface $attachmentStorage,
        private readonly string $topic,
        private readonly SourceContract $source,
    ) {}

    /**
     * @throws AttachmentStorageFailedException|JsonException|Exception
     */
    public function sendEmailMessage(EmailRequestDto $requestDto): bool
    {
        $uploadedAttachments = $this->uploadAttachments($requestDto->attachments);

        $emailMessageDto = new EmailMessageDto(
            $this->source,
            $requestDto->from,
            $requestDto->to,
            $requestDto->subject,
            $requestDto->body,
            $requestDto->getEventId(),
            $uploadedAttachments,
        );

        return $this->sendToKafka($emailMessageDto);
    }

    /**
     * @throws AttachmentStorageFailedException
     */
    private function uploadAttachments(?AttachmentCollection $attachments): array
    {
        $uploadedAttachments = [];

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $uploadedAttachments[] = $this->attachmentStorage->storeAttachment($attachment);
            }
        }

        return $uploadedAttachments;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function sendToKafka(EmailMessageDto $messageDto): bool
    {
        $message = new Message(
            topicName: $this->topic,
            body: json_encode($messageDto->toArray(), JSON_THROW_ON_ERROR)
        );

        return Kafka::publish()
            ->withMessage($message)
            ->send();
    }
}
