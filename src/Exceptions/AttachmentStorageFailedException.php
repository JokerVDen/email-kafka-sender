<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Exceptions;


use Throwable;

class AttachmentStorageFailedException extends AttachmentStorageException
{
    public function __construct(
        string $fileName,
        string $message = 'Failed to save attachment',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message . ' : ' . $fileName, $code, $previous);
    }
}
