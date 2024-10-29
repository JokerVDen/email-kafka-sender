<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\DTOs;

use JokerVDen\EmailKafkaSender\Contracts\SourceContract;

readonly class EmailMessageDto
{
    public function __construct(
        public SourceContract $source,
        public string $from,
        public string $to,
        public string $subject,
        public string $body,
        public string $event_id,
        public array $attachments = []
    ) {}

    public function toArray(): array
    {
        return [
            'source' => $this->source->value(),
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'body' => $this->body,
            'attachments' => array_map(
                static fn(AttachmentDataDto $attachment) => $attachment->toArray(),
                $this->attachments,
            ),
            'event_id' => $this->event_id,
        ];
    }
}
