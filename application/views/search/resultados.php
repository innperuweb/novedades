<?php if (!empty($resultados)): ?>
    <div class="resultados-busqueda">
        <?php foreach ($resultados as $p): ?>
            <?php
            $nombre = htmlspecialchars($p['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $precio = isset($p['precio']) ? number_format((float) $p['precio'], 2) : number_format(0, 2);
            $imagen = rawurlencode((string) ($p['imagen'] ?? ''));
            $productoId = rawurlencode((string) ($p['id'] ?? ''));
            ?>
            <div class="producto">
                <img src="<?= asset_url('img/productos/' . $imagen); ?>" alt="<?= $nombre; ?>">
                <h4><?= $nombre; ?></h4>
                <p>S/ <?= $precio; ?></p>
                <a href="<?= base_url('producto/detalle/' . $productoId); ?>" class="btn-ver">Ver detalle</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No se encontraron productos.</p>
<?php endif; ?>
