<?php

declare(strict_types=1);

// Initialize writable /tmp directories required by Laravel in serverless environments
$tmpStorageDirectories = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($tmpStorageDirectories as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Handle SQLite database in /tmp if external database is not configured
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
if ($dbConnection === 'sqlite') {
    $tmpDatabase = '/tmp/database.sqlite';
    if (! file_exists($tmpDatabase)) {
        $sourceDatabase = __DIR__.'/../database/database.sqlite';
        if (file_exists($sourceDatabase)) {
            @copy($sourceDatabase, $tmpDatabase);
        } else {
            @touch($tmpDatabase);
        }
    }
    putenv("DB_DATABASE={$tmpDatabase}");
    $_ENV['DB_DATABASE'] = $tmpDatabase;
    $_SERVER['DB_DATABASE'] = $tmpDatabase;
}

// Forward serverless invocation to Laravel's public entry point
require __DIR__.'/../public/index.php';
