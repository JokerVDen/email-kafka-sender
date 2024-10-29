<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Collections;

use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class AttachmentCollection extends Collection
{
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof AttachmentDto) {
                throw new InvalidArgumentException('All elements of the collection must be instances of AttachmentDto');
            }
        }

        parent::__construct($items);
    }

    public function add($item): self
    {
        if (!$item instanceof AttachmentDto) {
            throw new InvalidArgumentException('The element must be an instance of AttachmentDto');
        }

        return parent::add($item);
    }
}
