<?php

declare(strict_types=1);

// Flag Vercel environment
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

if (! getenv('APP_MAINTENANCE_DRIVER')) {
    putenv('APP_MAINTENANCE_DRIVER=file');
    $_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
    $_SERVER['APP_MAINTENANCE_DRIVER'] = 'file';
}

$appDebug = getenv('APP_DEBUG');
if ($appDebug === false || $appDebug === '') {
    putenv('APP_DEBUG=false');
    $_ENV['APP_DEBUG'] = 'false';
    $_SERVER['APP_DEBUG'] = 'false';
}

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

// Configure database session driver and stable session settings
if (! getenv('SESSION_DRIVER') || getenv('SESSION_DRIVER') === 'cookie' || getenv('SESSION_DRIVER') === 'file') {
    putenv('SESSION_DRIVER=database');
    $_ENV['SESSION_DRIVER'] = 'database';
    $_SERVER['SESSION_DRIVER'] = 'database';
}

if (! getenv('SESSION_COOKIE')) {
    putenv('SESSION_COOKIE=kb_finvest_session');
    $_ENV['SESSION_COOKIE'] = 'kb_finvest_session';
    $_SERVER['SESSION_COOKIE'] = 'kb_finvest_session';
}

if (! getenv('SESSION_LIFETIME') || getenv('SESSION_LIFETIME') === '0') {
    putenv('SESSION_LIFETIME=120');
    $_ENV['SESSION_LIFETIME'] = '120';
    $_SERVER['SESSION_LIFETIME'] = '120';
}

if (! getenv('APP_NAME')) {
    putenv('APP_NAME=KB Finvest');
    $_ENV['APP_NAME'] = 'KB Finvest';
    $_SERVER['APP_NAME'] = 'KB Finvest';
}

if (! getenv('CACHE_STORE') || getenv('CACHE_STORE') === 'file') {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
    $_SERVER['CACHE_STORE'] = 'array';
}

if (! getenv('MAKE_BOOKING_WEBHOOK_URL')) {
    $makeWebhookUrl = 'https://hook.eu1.make.com/899tbmofa1ts5nqdenlq16t5abhtgd1b';
    putenv("MAKE_BOOKING_WEBHOOK_URL={$makeWebhookUrl}");
    $_ENV['MAKE_BOOKING_WEBHOOK_URL'] = $makeWebhookUrl;
    $_SERVER['MAKE_BOOKING_WEBHOOK_URL'] = $makeWebhookUrl;
    putenv("ZAPIER_BOOKING_WEBHOOK_URL={$makeWebhookUrl}");
    $_ENV['ZAPIER_BOOKING_WEBHOOK_URL'] = $makeWebhookUrl;
    $_SERVER['ZAPIER_BOOKING_WEBHOOK_URL'] = $makeWebhookUrl;
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
