<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

try {
    $productos = fetch_all('SELECT id, codigo, nombre, stock, unidad FROM productos ORDER BY nombre ASC');
    $movimientos = fetch_all(
        'SELECT TOP 20 m.id, m.tipo, m.cantidad, m.motivo, m.usuario, m.creado_en, p.codigo, p.nombre
         FROM movimientos m
         INNER JOIN productos p ON p.id = m.producto_id
         ORDER BY m.creado_en DESC'
    );
} catch (Throwable $exception) {
    render_connection_error($exception);
    return;
}

render_header('Movimientos');
?>
<section class="grid-2">
    <article class="panel">
        <h3>Registrar movimiento</h3>
        <form method="post" action="registrar_movimiento.php" class="form-grid">
            <label>
                Producto
                <select name="producto_id" required>
                    <option value="">Selecciona un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= h((string) $producto['id']) ?>">
                            <?= h($producto['codigo'] . ' - ' . $producto['nombre'] . ' (' . $producto['stock'] . ' ' . $producto['unidad'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Tipo
                <select name="tipo" required>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </select>
            </label>
            <label>
                Cantidad
                <input type="number" name="cantidad" min="1" step="1" required>
            </label>
            <label>
                Usuario
                <input type="text" name="usuario" maxlength="100" value="admin">
            </label>
            <label>
                Motivo
                <textarea name="motivo" rows="4" maxlength="255" placeholder="Compra, ajuste, consumo interno, venta..."></textarea>
            </label>
            <button type="submit">Registrar movimiento</button>
        </form>
    </article>

    <article class="panel">
        <div class="panel-heading">
            <h3>Historial reciente</h3>
            <span class="badge"><?= count($movimientos) ?> registros</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?= h((string) $movimiento['creado_en']) ?></td>
                        <td><?= h($movimiento['codigo'] . ' - ' . $movimiento['nombre']) ?></td>
                        <td><span class="badge <?= $movimiento['tipo'] === 'entrada' ? 'badge-ok' : 'badge-danger' ?>"><?= h($movimiento['tipo']) ?></span></td>
                        <td><?= h((string) $movimiento['cantidad']) ?></td>
                        <td><?= h($movimiento['motivo']) ?></td>
                        <td><?= h($movimiento['usuario']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
<?php
render_footer();
