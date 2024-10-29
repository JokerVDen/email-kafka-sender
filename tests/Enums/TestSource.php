<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Tests\Enums;

use JokerVDen\EmailKafkaSender\Contracts\SourceContract;

enum TestSource: string implements SourceContract
{
    case TEST_SOURCE = 'test_source';

    public function value(): string
    {
        return $this->value;
    }
}
