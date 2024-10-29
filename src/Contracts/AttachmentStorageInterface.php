<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Contracts;

use JokerVDen\EmailKafkaSender\DTOs\AttachmentDataDto;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;

interface AttachmentStorageInterface
{
    /**
     * @throws AttachmentStorageFailedException
     */
    public function storeAttachment(AttachmentDto $attachment): AttachmentDataDto;
}
