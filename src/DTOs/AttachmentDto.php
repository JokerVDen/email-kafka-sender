<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\DTOs;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

readonly class AttachmentDto
{
    public function __construct(
        public File|UploadedFile $file,
        public string $fileName
    ) {}
}
