<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Exceptions;

namespace JokerVDen\EmailKafkaSender\Exceptions;

class UnsupportedDriverException extends \Exception
{
    public function __construct(string $driver)
    {
        $message = 'Unsupported driver for storing attachments: ' . $driver;

        parent::__construct($message);
    }
}
