<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Providers;

use Illuminate\Support\ServiceProvider;
use JokerVDen\EmailKafkaSender\Contracts\AttachmentStorageInterface;
use JokerVDen\EmailKafkaSender\Exceptions\UnsupportedDriverException;
use JokerVDen\EmailKafkaSender\Services\AttachmentStorages\LocalAttachmentStorage;
use JokerVDen\EmailKafkaSender\Services\AttachmentStorages\S3AttachmentStorage;

class EmailKafkaSenderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/email-kafka-sender.php', 'email-kafka-sender');

        $this->app->bind(AttachmentStorageInterface::class, function () {
            $driver = config('email-kafka-sender.storage_driver');
            $storageDirectory = config('email-kafka-sender.storage_directory');

            return match ($driver) {
                's3' => new S3AttachmentStorage($storageDirectory),
                'local' => new LocalAttachmentStorage($storageDirectory),
                default => throw new UnsupportedDriverException($driver),
            };
        });
    }
}
