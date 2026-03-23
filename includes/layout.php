<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function render_header(string $title): void
{
    $flash = get_flash();
    $appName = app_config()['app']['name'];
    ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> | <?= h($appName) ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div>
            <p class="eyebrow">Azure SQL</p>
            <h1><?= h($appName) ?></h1>
            <p class="muted">Gestión simple de stock, categorías y movimientos.</p>
        </div>
        <nav class="nav">
            <a href="index.php">Dashboard</a>
            <a href="productos.php">Productos</a>
            <a href="categorias.php">Categorías</a>
            <a href="movimientos.php">Movimientos</a>
        </nav>
        <div class="status-card">
            <strong>Estado del driver</strong>
            <?php $driverStatus = db_driver_status(); ?>
            <span><?= $driverStatus['pdo_sqlsrv'] ? 'pdo_sqlsrv activo' : 'pdo_sqlsrv pendiente' ?></span>
            <span><?= $driverStatus['sqlsrv'] ? 'sqlsrv activo' : 'sqlsrv pendiente' ?></span>
        </div>
    </aside>
    <main class="content">
        <header class="page-header">
            <div>
                <p class="eyebrow">Panel</p>
                <h2><?= h($title) ?></h2>
            </div>
        </header>
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
    <?php
}

function render_footer(): void
{
    ?>
    </main>
</div>
</body>
</html>
    <?php
}

function render_connection_error(Throwable $exception): void
{
    render_header('Conexión no disponible');
    ?>
    <section class="panel">
        <h3>No se pudo conectar a Azure SQL</h3>
        <p>Actualiza la contraseña en <code>config.php</code> y habilita los drivers <code>sqlsrv</code> y <code>pdo_sqlsrv</code> en XAMPP.</p>
        <pre class="error-box"><?= h($exception->getMessage()) ?></pre>
    </section>
    <?php
    render_footer();
}
