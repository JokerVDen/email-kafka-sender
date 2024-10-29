<?php

declare(strict_types=1);

namespace JokerVDen\EmailKafkaSender\Tests\Services\AttachmentStorages;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDataDto;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use JokerVDen\EmailKafkaSender\Exceptions\AttachmentStorageFailedException;
use JokerVDen\EmailKafkaSender\Services\AttachmentStorages\LocalAttachmentStorage;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LocalAttachmentStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function store_attachment_successfully(): void
    {
        $storageDirectory = 'attachments';
        $storageService = new LocalAttachmentStorage($storageDirectory);

        $fileContent = 'Test file content';
        $filePath = $storageDirectory . '/somefile.txt';
        Storage::disk('public')
            ->put($filePath, $fileContent);

        $file = new File(Storage::disk('public')->path($filePath));
        $attachmentDto = new AttachmentDto($file, 'somefile.txt');

        $result = $storageService->storeAttachment($attachmentDto);

        $this->assertInstanceOf(AttachmentDataDto::class, $result);
        $this->assertEquals('somefile.txt', $result->fileName);
        $this->assertEquals(Storage::disk('public')->url("{$storageDirectory}/somefile.txt"), $result->url);
    }

    #[Test]
    public function store_attachment_fails(): void
    {
        $this->expectException(AttachmentStorageFailedException::class);

        $storageDirectory = 'attachments';
        $storageService = new LocalAttachmentStorage($storageDirectory);

        // Use the real path to the temporarily created file
        $file = tmpfile();
        $filePath = stream_get_meta_data($file)['uri'];
        fwrite($file, 'Test file content');

        $attachmentDto = new AttachmentDto(new File($filePath), 'somefile.txt');

        // Directly call `putFileAs` with `false` to simulate save failure
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf()
            ->shouldReceive('putFileAs')
            ->andReturn(false);

        $storageService->storeAttachment($attachmentDto);

        // Close the temporary file after the test
        fclose($file);
    }
}
