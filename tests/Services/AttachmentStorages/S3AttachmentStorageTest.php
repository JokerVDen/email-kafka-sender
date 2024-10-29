<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Tests\Services\AttachmentStorages;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDataDto;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;
use JokerVDen\EmailKafkaSender\Services\AttachmentStorages\S3AttachmentStorage;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class S3AttachmentStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
    }

    #[Test]
    public function store_attachment_successfully(): void
    {
        $storageDirectory = 'attachments';
        $storageService = new S3AttachmentStorage($storageDirectory);

        $fileContent = 'Test file content';
        $filePath = $storageDirectory . '/somefile.txt';
        Storage::disk('s3')->put($filePath, $fileContent);

        $file = new File(Storage::disk('s3')->path($filePath));
        $attachmentDto = new AttachmentDto($file, 'somefile.txt');

        $result = $storageService->storeAttachment($attachmentDto);

        $this->assertInstanceOf(AttachmentDataDto::class, $result);
        $this->assertEquals('somefile.txt', $result->fileName);
        $this->assertEquals(Storage::disk('s3')->url("{$storageDirectory}/somefile.txt"), $result->url);
    }

    #[Test]
    public function store_attachment_fails(): void
    {
        $this->expectException(AttachmentStorageFailedException::class);

        $storageDirectory = 'attachments';
        $storageService = new S3AttachmentStorage($storageDirectory);

        $file = tmpfile();
        $filePath = stream_get_meta_data($file)['uri'];
        fwrite($file, 'Test file content');

        $attachmentDto = new AttachmentDto(new File($filePath), 'somefile.txt');

        // Configure the S3 disk so that the `putFileAs` method returns false, simulating failure
        Storage::shouldReceive('disk')
            ->with('s3')
            ->andReturnSelf()
            ->shouldReceive('putFileAs')
            ->andReturn(false);

        $storageService->storeAttachment($attachmentDto);

        fclose($file); // Close the temporary file
    }
}
