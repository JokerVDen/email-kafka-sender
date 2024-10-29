<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\DTOs;

use JokerVDen\EmailKafkaSender\Collections\AttachmentCollection;
use JokerVDen\EmailKafkaSender\Contracts\SourceContract;
use Ramsey\Uuid\Uuid;

readonly class EmailRequestDto
{
    private string $eventId;

    public function __construct(
        public string $from,
        public string $to,
        public string $subject,
        public string $body,
        public ?AttachmentCollection $attachments = null
    ) {
        $this->eventId = Uuid::uuid7()->toString();
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }
}
