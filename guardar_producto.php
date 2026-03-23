<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('productos.php');
}

$codigo = trim($_POST['codigo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$categoriaId = trim($_POST['categoria_id'] ?? '');
$precio = (float) ($_POST['precio'] ?? 0);
$stock = (int) ($_POST['stock'] ?? 0);
$stockMinimo = (int) ($_POST['stock_minimo'] ?? 5);
$unidad = trim($_POST['unidad'] ?? 'unidad');

if ($codigo === '' || $nombre === '') {
    set_flash('error', 'Código y nombre son obligatorios.');
    redirect('productos.php');
}

try {
    execute_query(
        'INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio, stock, stock_minimo, unidad, actualizado_en)
         VALUES (:codigo, :nombre, :descripcion, :categoria_id, :precio, :stock, :stock_minimo, :unidad, GETDATE())',
        [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion !== '' ? $descripcion : null,
            'categoria_id' => $categoriaId !== '' ? (int) $categoriaId : null,
            'precio' => $precio,
            'stock' => $stock,
            'stock_minimo' => $stockMinimo,
            'unidad' => $unidad !== '' ? $unidad : 'unidad',
        ]
    );

    set_flash('success', 'Producto creado correctamente.');
} catch (Throwable $exception) {
    set_flash('error', 'No se pudo guardar el producto: ' . $exception->getMessage());
}

redirect('productos.php');
