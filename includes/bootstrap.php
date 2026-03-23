<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/../config.php';

function app_config(): array
{
    global $config;

    return $config;
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (! isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function db_driver_status(): array
{
    return [
        'pdo_sqlsrv' => extension_loaded('pdo_sqlsrv'),
        'sqlsrv' => extension_loaded('sqlsrv'),
    ];
}
