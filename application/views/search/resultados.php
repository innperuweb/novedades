<?php
if (!isset($productoModel) || !($productoModel instanceof ProductoModel)) {
    if (!class_exists('ProductoModel')) {
        require_once APP_PATH . '/models/ProductoModel.php';
    }
    if (class_exists('ProductoModel')) {
        $productoModel = new ProductoModel();
    }
}
?>
<?php if (!empty($resultados)): ?>
    <div class="resultados-busqueda">
        <?php foreach ($resultados as $p): ?>
            <?php
            $nombre     = htmlspecialchars($p['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $precio     = isset($p['precio']) ? number_format((float) $p['precio'], 2) : number_format(0, 2);
            $productoId = (int) ($p['id'] ?? 0);
            $detalleUrl = htmlspecialchars(
                base_url('productos/detalle?id=' . urlencode((string) $productoId)),
                ENT_QUOTES,
                'UTF-8'
            );
            $urlImagen = $productoModel instanceof ProductoModel
                ? htmlspecialchars($productoModel->urlImagenPrincipalDeFila($p), ENT_QUOTES, 'UTF-8')
                : htmlspecialchars(asset_url('img/no-image.jpg'), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="producto">
                <img src="<?= $urlImagen; ?>" alt="<?= $nombre; ?>">
                <h4><?= $nombre; ?></h4>
                <p>S/ <?= $precio; ?></p>
                <a href="<?= $detalleUrl; ?>" class="btn-ver">Ver detalle</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No se encontraron productos.</p>
<?php endif; ?>