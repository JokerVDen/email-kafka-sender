<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Tests\Collections;

use Illuminate\Http\File;
use InvalidArgumentException;
use JokerVDen\EmailKafkaSender\Collections\AttachmentCollection;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AttachmentCollectionTest extends TestCase
{
    #[Test]
    public function constructor_accepts_only_attachment_dto_instances(): void
    {
        $fileMock = $this->createMock(File::class);

        $attachment1 = new AttachmentDto($fileMock, 'file1.png');
        $attachment2 = new AttachmentDto($fileMock, 'file2.png');

        $collection = new AttachmentCollection([
            $attachment1,
            $attachment2,
        ]);

        $this->assertCount(2, $collection);
        $this->assertInstanceOf(AttachmentDto::class, $collection[0]);
    }

    #[Test]
    public function add_method_accepts_only_attachment_dto_instances(): void
    {
        $collection = new AttachmentCollection();

        $fileMock = $this->createMock(File::class);
        $attachment = new AttachmentDto($fileMock, 'file.png');

        $collection->add($attachment);

        $this->assertCount(1, $collection);
        $this->assertSame($attachment, $collection->first());
    }
    #[Test]
    public function add_method_throws_exception_for_invalid_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $collection = new AttachmentCollection();
        $collection->add('not_an_attachment');
    }


    #[Test]
    public function constructor_throws_exception_for_invalid_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All elements of the collection must be instances of AttachmentDto');

        new AttachmentCollection(['not_an_attachment']);
    }
}
