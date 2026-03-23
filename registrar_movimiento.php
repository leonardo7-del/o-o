<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('movimientos.php');
}

$productoId = (int) ($_POST['producto_id'] ?? 0);
$tipo = trim($_POST['tipo'] ?? '');
$cantidad = (int) ($_POST['cantidad'] ?? 0);
$motivo = trim($_POST['motivo'] ?? '');
$usuario = trim($_POST['usuario'] ?? 'admin');

if ($productoId <= 0 || ! in_array($tipo, ['entrada', 'salida'], true) || $cantidad <= 0) {
    set_flash('error', 'Completa correctamente los datos del movimiento.');
    redirect('movimientos.php');
}

try {
    $pdo = db();
    $pdo->beginTransaction();

    $producto = fetch_one('SELECT id, nombre, stock FROM productos WHERE id = :id', ['id' => $productoId]);

    if ($producto === null) {
        throw new RuntimeException('El producto no existe.');
    }

    $nuevoStock = $tipo === 'entrada'
        ? (int) $producto['stock'] + $cantidad
        : (int) $producto['stock'] - $cantidad;

    if ($nuevoStock < 0) {
        throw new RuntimeException('La salida supera el stock disponible.');
    }

    execute_query(
        'INSERT INTO movimientos (producto_id, tipo, cantidad, motivo, usuario)
         VALUES (:producto_id, :tipo, :cantidad, :motivo, :usuario)',
        [
            'producto_id' => $productoId,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => $motivo !== '' ? $motivo : null,
            'usuario' => $usuario !== '' ? $usuario : 'admin',
        ]
    );

    execute_query(
        'UPDATE productos SET stock = :stock, actualizado_en = GETDATE() WHERE id = :id',
        [
            'stock' => $nuevoStock,
            'id' => $productoId,
        ]
    );

    $pdo->commit();

    set_flash('success', 'Movimiento registrado y stock actualizado.');
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    set_flash('error', 'No se pudo registrar el movimiento: ' . $exception->getMessage());
}

redirect('movimientos.php');
