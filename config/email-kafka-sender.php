<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Topic kafka
    |--------------------------------------------------------------------------
    | Specify the topic to which messages will be sent
    */
    'topic' => env('EMAIL_KAFKA_SENDER_KAFKA_TOPIC', 'email_service_topic'),

    /*
    |--------------------------------------------------------------------------
    | Storage Driver for Attachments
    |--------------------------------------------------------------------------
    | Here you can specify the driver that will be used for storing
    | attachments. Available values: "local", "s3".
    */
    'storage_driver' => env('ATTACHMENT_STORAGE_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Directory for Storing Attachments
    |--------------------------------------------------------------------------
    | Specify the directory where attachments will be saved.
    */
    'storage_directory' => env('ATTACHMENT_STORAGE_DIRECTORY', 'email_attachments'),
];
