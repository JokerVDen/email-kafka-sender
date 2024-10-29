<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Tests\Providers;

use Illuminate\Support\Facades\Config;
use JokerVDen\EmailKafkaSender\Contracts\AttachmentStorageInterface;
use JokerVDen\EmailKafkaSender\Exceptions\UnsupportedDriverException;
use JokerVDen\EmailKafkaSender\Providers\EmailKafkaSenderServiceProvider;
use JokerVDen\EmailKafkaSender\Services\AttachmentStorages\LocalAttachmentStorage;
use JokerVDen\EmailKafkaSender\Services\AttachmentStorages\S3AttachmentStorage;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EmailKafkaSenderServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [EmailKafkaSenderServiceProvider::class];
    }

    #[Test]
    public function binds_local_attachment_storage_when_local_driver_is_configured(): void
    {
        Config::set('email-kafka-sender.storage_driver', 'local');
        Config::set('email-kafka-sender.storage_directory', 'local-directory');

        $storage = $this->app->make(AttachmentStorageInterface::class);

        $this->assertInstanceOf(LocalAttachmentStorage::class, $storage);
    }

    #[Test]
    public function binds_s3_attachment_storage_when_s3_driver_is_configured(): void
    {
        Config::set('email-kafka-sender.storage_driver', 's3');
        Config::set('email-kafka-sender.storage_directory', 's3-directory');

        $storage = $this->app->make(AttachmentStorageInterface::class);

        $this->assertInstanceOf(S3AttachmentStorage::class, $storage);
    }

    #[Test]
    public function throws_unsupported_driver_exception_for_unsupported_driver(): void
    {
        Config::set('email-kafka-sender.storage_driver', 'unsupported_driver');

        $this->expectException(UnsupportedDriverException::class);
        $this->expectExceptionMessage('unsupported_driver');

        $this->app->make(AttachmentStorageInterface::class);
    }
}
