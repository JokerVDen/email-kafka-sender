<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\DTOs;

readonly class AttachmentDataDto
{
    public function __construct(
        public string $fileName,
        public string $url,
    ) {}

    public function toArray(): array
    {
        return [
            'fileName' => $this->fileName,
            'url' => $this->url,
        ];
    }
}
