<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

try {
    $categorias = fetch_all('SELECT id, nombre, descripcion, creado_en FROM categorias ORDER BY nombre ASC');
} catch (Throwable $exception) {
    render_connection_error($exception);
    return;
}

render_header('Categorías');
?>
<section class="grid-2">
    <article class="panel">
        <h3>Nueva categoría</h3>
        <form method="post" action="guardar_categoria.php" class="form-grid">
            <label>
                Nombre
                <input type="text" name="nombre" maxlength="100" required>
            </label>
            <label>
                Descripción
                <textarea name="descripcion" rows="4" maxlength="255"></textarea>
            </label>
            <button type="submit">Guardar categoría</button>
        </form>
    </article>

    <article class="panel">
        <div class="panel-heading">
            <h3>Listado</h3>
            <span class="badge"><?= count($categorias) ?> registros</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Creado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= h((string) $categoria['id']) ?></td>
                        <td><?= h($categoria['nombre']) ?></td>
                        <td><?= h($categoria['descripcion']) ?></td>
                        <td><?= h((string) $categoria['creado_en']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
<?php
render_footer();
