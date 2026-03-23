<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

try {
    $categorias = fetch_all('SELECT id, nombre FROM categorias ORDER BY nombre ASC');
    $productos = fetch_all(
        'SELECT p.id, p.codigo, p.nombre, p.descripcion, p.precio, p.stock, p.stock_minimo, p.unidad,
                p.creado_en, c.nombre AS categoria
         FROM productos p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         ORDER BY p.nombre ASC'
    );
} catch (Throwable $exception) {
    render_connection_error($exception);
    return;
}

render_header('Productos');
?>
<section class="grid-2">
    <article class="panel">
        <h3>Nuevo producto</h3>
        <form method="post" action="guardar_producto.php" class="form-grid">
            <label>
                Código
                <input type="text" name="codigo" maxlength="50" required>
            </label>
            <label>
                Nombre
                <input type="text" name="nombre" maxlength="150" required>
            </label>
            <label>
                Categoría
                <select name="categoria_id">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= h((string) $categoria['id']) ?>"><?= h($categoria['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Precio
                <input type="number" name="precio" min="0" step="0.01" value="0" required>
            </label>
            <label>
                Stock inicial
                <input type="number" name="stock" min="0" step="1" value="0" required>
            </label>
            <label>
                Stock mínimo
                <input type="number" name="stock_minimo" min="0" step="1" value="5" required>
            </label>
            <label>
                Unidad
                <input type="text" name="unidad" maxlength="30" value="unidad">
            </label>
            <label>
                Descripción
                <textarea name="descripcion" rows="4" maxlength="500"></textarea>
            </label>
            <button type="submit">Guardar producto</button>
        </form>
    </article>

    <article class="panel">
        <div class="panel-heading">
            <h3>Inventario actual</h3>
            <span class="badge"><?= count($productos) ?> productos</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Alerta</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $producto): ?>
                    <?php $critical = (int) $producto['stock'] <= (int) $producto['stock_minimo']; ?>
                    <tr>
                        <td><?= h($producto['codigo']) ?></td>
                        <td>
                            <strong><?= h($producto['nombre']) ?></strong>
                            <small class="block-muted"><?= h($producto['descripcion']) ?></small>
                        </td>
                        <td><?= h($producto['categoria'] ?? 'Sin categoría') ?></td>
                        <td>S/ <?= h(number_format((float) $producto['precio'], 2)) ?></td>
                        <td><?= h((string) $producto['stock']) ?> <?= h($producto['unidad']) ?></td>
                        <td>
                            <span class="badge <?= $critical ? 'badge-danger' : 'badge-ok' ?>">
                                <?= $critical ? 'Stock bajo' : 'Normal' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
<?php
render_footer();
