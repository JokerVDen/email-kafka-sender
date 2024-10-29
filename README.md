# Email Kafka Sender

**Email Kafka Sender** is a Laravel package for sending email messages through Kafka. This package enables
microservice-based email messaging with attachment support, configurable storage options (local or S3), and Kafka
message publishing.

## Installation

Install the package via Composer:

```bash
composer require jokervden/email-kafka-sender
```

## Configuration

After installation, Laravel will automatically register the `EmailKafkaSenderServiceProvider`. To customize the package
settings, publish the configuration file:

```bash
php artisan vendor:publish --provider="JokerVDen\\EmailKafkaSender\\Providers\\EmailKafkaSenderServiceProvider"
```

### Configuration File

The configuration file `email-kafka-sender.php` includes the following options:

```php
return [
    'topic' => env('EMAIL_KAFKA_SENDER_KAFKA_TOPIC', 'email_service_topic'),
    'storage_driver' => env('ATTACHMENT_STORAGE_DRIVER', 'local'),
    'storage_directory' => env('ATTACHMENT_STORAGE_DIRECTORY', 'email_attachments'),
];
```

### Options

- **`topic`**: The Kafka topic where email messages will be published.
- **`storage_driver`**: The storage driver for attachments. Available options: `local` or `s3`.
- **`storage_directory`**: Directory where attachments will be saved.

Add these values to your `.env` file:

```env
EMAIL_KAFKA_SENDER_KAFKA_TOPIC=email_service_topic
ATTACHMENT_STORAGE_DRIVER=local
ATTACHMENT_STORAGE_DIRECTORY=email_attachments
```

### Initializing the Email Producer

It’s recommended to initialize the `EmailMessageProducerInterface` in a `ServiceProvider`. This ensures the producer is
automatically bound with the required dependencies and configurations. Add the following binding to your custom
`ServiceProvider`:

```php
use JokerVDen\EmailKafkaSender\Contracts\EmailMessageProducerInterface;
use JokerVDen\EmailKafkaSender\Services\EmailMessageProducer;
use Illuminate\Foundation\Application;

$this->app->bind(EmailMessageProducerInterface::class, function (Application $app) {
    return new EmailMessageProducer(
        $app->make(AttachmentStorageInterface::class),
        config('email-kafka-sender.topic'),
        TestSource::TEST_SOURCE, // Specify your SourceContract
    );
});
```

## Usage

### Creating an Email Producer

To use the Email Producer, retrieve an instance of `EmailMessageProducerInterface`:

```php
use JokerVDen\EmailKafkaSender\Contracts\EmailMessageProducerInterface;
use JokerVDen\EmailKafkaSender\DTOs\EmailRequestDto;
use JokerVDen\EmailKafkaSender\Collections\AttachmentCollection;
use JokerVDen\EmailKafkaSender\DTOs\AttachmentDto;
use Illuminate\Http\UploadedFile;

$emailProducer = app(EmailMessageProducerInterface::class);

// Example of sending a message
$attachments = new AttachmentCollection([
    new AttachmentDto(new UploadedFile('/path/to/file1.pdf', 'file1.pdf'), 'file1.pdf'),
    new AttachmentDto(new UploadedFile('/path/to/file2.jpg', 'file2.jpg'), 'file2.jpg')
]);

$requestDto = new EmailRequestDto(
from: "sender@example.com",
to: "recipient@example.com",
subject: "Your Subject",
body: "This is the body of the email.",
attachments: $attachments
);

$emailProducer->sendEmailMessage($requestDto);
```

### Interfaces and DTOs

- **`EmailRequestDto`**: Used for creating an email request. It includes sender, recipient, subject, message body, and
  attachments.
- **`EmailMessageDto`**: Represents the data for Kafka publishing, including `source`, `from`, `to`, `subject`, `body`,
  `event_id`, and `attachments`.
- **`AttachmentDto`** and **`AttachmentDataDto`**: DTOs for handling attachment data.

### Exceptions

The package includes custom exceptions for more granular error handling:

- **`AttachmentStorageException`**: The base exception for all attachment storage errors.
- **`AttachmentStorageFailedException`**: Thrown when a file fails to be stored.

## Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 10 or higher
- **mateusjunges/laravel-kafka**: For Kafka integration

## Testing

The package includes testing support via `orchestra/testbench`:

```bash
composer test
```
