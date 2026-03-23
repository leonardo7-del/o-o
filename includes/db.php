<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $driverStatus = db_driver_status();

    if (! $driverStatus['pdo_sqlsrv']) {
        throw new RuntimeException(
            'El driver pdo_sqlsrv no está habilitado en este PHP. Instala o activa pdo_sqlsrv/sqlsrv en XAMPP antes de conectar con Azure SQL.'
        );
    }

    $db = app_config()['db'];

    $dsn = sprintf(
        'sqlsrv:server=%s;Database=%s;LoginTimeout=%d;Encrypt=%s;TrustServerCertificate=%s',
        $db['server'],
        $db['database'],
        $db['login_timeout'],
        $db['encrypt'] ? '1' : '0',
        $db['trust_server_certificate'] ? '1' : '0'
    );

    $pdo = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function fetch_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function fetch_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function execute_query(string $sql, array $params = []): void
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
}
