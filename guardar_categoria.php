<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categorias.php');
}

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($nombre === '') {
    set_flash('error', 'El nombre de la categoría es obligatorio.');
    redirect('categorias.php');
}

try {
    execute_query(
        'INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)',
        [
            'nombre' => $nombre,
            'descripcion' => $descripcion !== '' ? $descripcion : null,
        ]
    );

    set_flash('success', 'Categoría creada correctamente.');
} catch (Throwable $exception) {
    set_flash('error', 'No se pudo guardar la categoría: ' . $exception->getMessage());
}

redirect('categorias.php');
