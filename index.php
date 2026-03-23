<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

try {
    $totals = fetch_one(
        'SELECT
            (SELECT COUNT(*) FROM categorias) AS total_categorias,
            (SELECT COUNT(*) FROM productos) AS total_productos,
            (SELECT COUNT(*) FROM movimientos) AS total_movimientos,
            (SELECT ISNULL(SUM(stock), 0) FROM productos) AS stock_total'
    );

    $lowStock = fetch_all(
        'SELECT TOP 5 p.id, p.codigo, p.nombre, p.stock, p.stock_minimo, c.nombre AS categoria
         FROM productos p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.stock <= p.stock_minimo
         ORDER BY p.stock ASC, p.nombre ASC'
    );

    $recentMoves = fetch_all(
        'SELECT TOP 8 m.tipo, m.cantidad, m.motivo, m.usuario, m.creado_en, p.nombre AS producto
         FROM movimientos m
         INNER JOIN productos p ON p.id = m.producto_id
         ORDER BY m.creado_en DESC'
    );
} catch (Throwable $exception) {
    render_connection_error($exception);
    return;
}

render_header('Dashboard');
?>
<section class="stats-grid">
    <article class="stat-card">
        <span>Categorías</span>
        <strong><?= h((string) $totals['total_categorias']) ?></strong>
    </article>
    <article class="stat-card">
        <span>Productos</span>
        <strong><?= h((string) $totals['total_productos']) ?></strong>
    </article>
    <article class="stat-card">
        <span>Movimientos</span>
        <strong><?= h((string) $totals['total_movimientos']) ?></strong>
    </article>
    <article class="stat-card">
        <span>Stock total</span>
        <strong><?= h((string) $totals['stock_total']) ?></strong>
    </article>
</section>

<section class="grid-2">
    <article class="panel">
        <div class="panel-heading">
            <h3>Productos con stock bajo</h3>
            <a class="button-link" href="productos.php">Ver productos</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($lowStock === []): ?>
                    <tr><td colspan="4">No hay alertas de stock por ahora.</td></tr>
                <?php endif; ?>
                <?php foreach ($lowStock as $item): ?>
                    <tr>
                        <td><?= h($item['codigo']) ?></td>
                        <td><?= h($item['nombre']) ?></td>
                        <td><?= h($item['categoria'] ?? 'Sin categoría') ?></td>
                        <td><?= h((string) $item['stock']) ?> / mín. <?= h((string) $item['stock_minimo']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="panel">
        <div class="panel-heading">
            <h3>Últimos movimientos</h3>
            <a class="button-link" href="movimientos.php">Registrar</a>
        </div>
        <div class="activity-list">
            <?php if ($recentMoves === []): ?>
                <p>No hay movimientos registrados.</p>
            <?php endif; ?>
            <?php foreach ($recentMoves as $move): ?>
                <div class="activity-item">
                    <strong><?= h($move['producto']) ?></strong>
                    <span><?= h(ucfirst($move['tipo'])) ?> de <?= h((string) $move['cantidad']) ?> unidades</span>
                    <small><?= h((string) $move['creado_en']) ?> · <?= h($move['usuario']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
<?php
render_footer();
