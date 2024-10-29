<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Services\AttachmentStorages;

use Illuminate\Support\Facades\Storage;
use JokerVDen\EmailKafkaSender\Contracts\AttachmentStorageInterface;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDataDto;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;

class LocalAttachmentStorage implements AttachmentStorageInterface
{
    public function __construct(private readonly string $storageDirectory) {}

    /**
     * @throws AttachmentStorageFailedException
     */
    public function storeAttachment(AttachmentDto $attachment): AttachmentDataDto
    {
        $storedPath = Storage::disk('public')
            ->putFileAs($this->storageDirectory, $attachment->file, $attachment->fileName);

        if (!$storedPath) {
            throw new AttachmentStorageFailedException($attachment->fileName);
        }

        $url = Storage::disk('public')->url($storedPath);

        return new AttachmentDataDto($attachment->fileName, $url);
    }
}
