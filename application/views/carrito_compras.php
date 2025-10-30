<?php
$total = 0.0;
$carrito = isset($carrito) && is_array($carrito)
    ? $carrito
    : (isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) ? $_SESSION['carrito'] : []);
?>

<style>
.quantity-input {
    width: 60px !important;
    text-align: center;
    margin: 0 auto;
    display: block;
}
</style>

<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Carrito de compras</h1>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper">
    <div class="page-content-inner">
        <div class="container">
            <div class="row pt--80 pb--80 pt-md--45 pt-sm--25 pb-md--60 pb-sm--40">
                <div class="col-lg-8 mb-md--30">
                    <div class="cart-form">
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="table-content table-responsive">
                                    <table class="table text-center">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th class="text-start">Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($carrito)): ?>
                                                <?php foreach ($carrito as $item): ?>
                                                    <?php
                                                        $precio = (float) ($item['precio'] ?? 0);
                                                        $imagenRuta = 'img/products/' . ($item['imagen'] ?? 'no-image.jpg');
                                                        $uid = (string) ($item['uid'] ?? '');
                                                        $color = trim((string) ($item['color'] ?? ''));
                                                        $talla = trim((string) ($item['talla'] ?? ''));
                                                        $removeQuery = $uid !== ''
                                                            ? 'uid=' . urlencode($uid)
                                                            : 'id=' . urlencode((string) ($item['id'] ?? ''));
                                                    ?>
                                                    <tr>
                                                        <td class="product-remove text-start"><a href="<?= base_url('carrito/eliminar?' . $removeQuery) ?>"><i class="dl-icon-close"></i></a></td>
                                                        <td class="product-thumbnail text-start">
                                                            <img src="<?= asset_url($imagenRuta) ?>" alt="<?= e($item['nombre'] ?? 'Producto'); ?>">
                                                        </td>
                                                        <td class="product-name text-start wide-column">
                                                            <h3>
                                                                <?= e($item['nombre'] ?? 'Producto'); ?>
                                                            </h3>
                                                            <?php if ($color !== ''): ?>
                                                                <p>Color: <?= e($color) ?></p>
                                                            <?php endif; ?>

                                                            <?php if ($talla !== ''): ?>
                                                                <p>Talla: <?= e($talla) ?></p>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="product-price">
                                                            <span class="product-price-wrapper">
                                                                <span class="money">S/ <?= number_format($precio, 2) ?></span>
                                                            </span>
                                                        </td>
                                                        <td class="product-quantity">
                                                            <div class="quantity">
                                                                <?php
                                                                    $cantidad = isset($item['cantidad']) && is_numeric($item['cantidad']) && $item['cantidad'] > 0
                                                                        ? (int)$item['cantidad']
                                                                        : 1;
                                                                    $subtotal = $precio * $cantidad;
                                                                    $total += $subtotal;
                                                                ?>
                                                                <form method="POST" action="<?= base_url('carrito/actualizar') ?>" class="d-flex align-items-center">
                                                                    <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
                                                                    <input type="hidden" name="uid" value="<?= e($uid) ?>">
                                                                    <input type="number" class="quantity-input" name="cantidad" min="1" value="<?= e((string) $cantidad) ?>" data-id="<?= e((string) $item['id']) ?>">
                                                                </form>
                                                            </div>
                                                        </td>
                                                        <td class="product-price subtotal">
                                                            S/ <?= number_format($subtotal, 2) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Tu carrito está vacío.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cart-collaterals">
                        <div class="cart-totals">
                            <h4 class="mb--15"><b> RESUMEN DE PEDIDO </b> </h4>
                            <div class="table-content table-responsive">
                                <table class="table order-table">
                                    <tbody>
                                        <?php if (!empty($carrito)): ?>
                                                <?php foreach ($carrito as $item): ?>
                                                <?php
                                                    $cantidad = (int) ($item['cantidad'] ?? 0);
                                                    $precio = (float) ($item['precio'] ?? 0);
                                                    $cantidad = $cantidad > 0 ? $cantidad : 1;
                                                    $subtotal = $precio * $cantidad;
                                                    $color = trim((string) ($item['color'] ?? ''));
                                                    $talla = trim((string) ($item['talla'] ?? ''));
                                                ?>
                                                <tr>
                                                    <th>
                                                        <?= e($item['nombre'] ?? 'Producto'); ?>
                                                        <?php if ($color !== '' || $talla !== ''): ?>
                                                            <br>
                                                            <small>
                                                                <?php if ($color !== ''): ?>Color: <?= e($color) ?><?php endif; ?>
                                                                <?php if ($color !== '' && $talla !== ''): ?> — <?php endif; ?>
                                                                <?php if ($talla !== ''): ?>Talla: <?= e($talla) ?><?php endif; ?>
                                                            </small>
                                                        <?php endif; ?>
                                                        <br><strong><span class="resumen-cantidad" data-id="<?= e((string) $item['id']) ?>">x<?= e((string) $cantidad) ?></span></strong>
                                                    </th>
                                                    <td style="text-align: right;">S/ <?= number_format($subtotal, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr class="order-total">
                                                <th>Total</th>
                                                <td>
                                                    <span class="product-price-wrapper">
                                                        <span class="money">S/ <?= number_format($total, 2); ?></span>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <th colspan="2" class="text-center">Tu carrito está vacío.</th>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (!empty($carrito)): ?>
                            <a href="<?= base_url('checkout'); ?>" class="btn btn-fullwidth btn-style-1">
                                Continuar con la compra
                            </a>
                            <a href="<?= base_url('carrito/vaciar'); ?>" class="btn btn-fullwidth btn-style-2 mt--10">
                                Vaciar carrito
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.quantity-input');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const id = this.getAttribute('data-id');
            const value = this.value;
            const cantidad = parseFloat(value) || 0;

            const resumenSpan = document.querySelector(`.resumen-cantidad[data-id="${id}"]`);
            if (resumenSpan) {
                resumenSpan.textContent = 'x' + value;
            }

            const precioEl = this.closest('tr').querySelector('.money');
            if (precioEl) {
                const precio = parseFloat(precioEl.textContent.replace(/[^\d.]/g, '')) || 0;
                const subtotalEl = this.closest('tr').querySelector('.subtotal');
                if (subtotalEl) {
                    subtotalEl.textContent = 'S/ ' + (precio * cantidad).toFixed(2);
                }
            }
        });
    });
});
</script>
