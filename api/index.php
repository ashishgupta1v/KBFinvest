<?php

declare(strict_types=1);

// Flag Vercel environment
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Set cache files to /tmp/storage
$storagePath = '/tmp/storage';
putenv("APP_CONFIG_CACHE={$storagePath}/config.php");
putenv("APP_EVENTS_CACHE={$storagePath}/events.php");
putenv("APP_PACKAGES_CACHE={$storagePath}/packages.php");
putenv("APP_ROUTES_CACHE={$storagePath}/routes.php");
putenv("APP_SERVICES_CACHE={$storagePath}/services.php");
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");

// Initialize writable /tmp directories required by Laravel in serverless environments
$tmpStorageDirectories = [
    "{$storagePath}/app/public",
    "{$storagePath}/framework/cache/data",
    "{$storagePath}/framework/sessions",
    "{$storagePath}/framework/testing",
    "{$storagePath}/framework/views",
    "{$storagePath}/logs",
];

foreach ($tmpStorageDirectories as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Fallback session & cache drivers to stateless in serverless if needed
if (! getenv('SESSION_DRIVER') || getenv('SESSION_DRIVER') === 'file') {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
}

if (! getenv('CACHE_STORE') || getenv('CACHE_STORE') === 'file') {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
    $_SERVER['CACHE_STORE'] = 'array';
}

// Handle SQLite database in /tmp if external database is not configured
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
if ($dbConnection === 'sqlite') {
    $tmpDatabase = "{$storagePath}/database.sqlite";
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
