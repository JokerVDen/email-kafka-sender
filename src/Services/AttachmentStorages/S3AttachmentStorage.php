<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Services\AttachmentStorages;

use Illuminate\Support\Facades\Storage;
use JokerVDen\EmailKafkaSender\Contracts\AttachmentStorageInterface;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDataDto;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;

class S3AttachmentStorage implements AttachmentStorageInterface
{
    public function __construct(private readonly string $storageDirectory) {}

    public function storeAttachment(AttachmentDto $attachment): AttachmentDataDto
    {
        $storedPath = Storage::disk('s3')
            ->putFileAs($this->storageDirectory, $attachment->file, $attachment->fileName);

        if (!$storedPath) {
            throw new AttachmentStorageFailedException($attachment->fileName);
        }

        $url = Storage::disk('s3')->url($storedPath);

        return new AttachmentDataDto($attachment->fileName, $url);
    }
}
