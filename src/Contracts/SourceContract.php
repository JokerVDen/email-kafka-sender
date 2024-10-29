<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Contracts;

interface SourceContract
{
    public function value(): string;
}
