<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Contracts;

use JokerVDen\EmailKafkaSender\DTOs\EmailRequestDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;
use JsonException;

interface EmailMessageProducerInterface
{
    /**
     * @throws AttachmentStorageFailedException|JsonException
     */
    public function sendEmailMessage(EmailRequestDto $requestDto): bool;
}
