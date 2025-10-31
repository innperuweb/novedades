<?php
require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/OrdenModel.php';

$nro = $_GET['nro'] ?? '';
$orden = $orden ?? ($nro !== '' ? OrdenModel::obtenerOrdenCompleta((string) $nro) : null);
$detalle = is_array($orden['detalle'] ?? null) ? $orden['detalle'] : [];

if ($orden === null): ?>
    <p>Orden no encontrada.</p>
    <p><a href="<?= e(base_url('mi_cuenta')) ?>">Volver a mi cuenta</a></p>
<?php return; endif; ?>

<h3>Pedido #<?= e($orden['nro_orden']) ?></h3>
<p><strong>Cliente:</strong> <?= e($orden['nombre']) ?> <?= e($orden['apellidos']) ?></p>
<p><strong>Dirección:</strong> <?= e($orden['direccion']) ?>, <?= e($orden['distrito']) ?></p>
<p><strong>Método de envío:</strong> <?= e($orden['metodo_envio']) ?></p>
<p><strong>Método de pago:</strong> <?= e($orden['metodo_pago']) ?></p>

<table>
    <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
    <?php foreach ($detalle as $item): ?>
        <tr>
            <td><?= e($item['nombre']) ?></td>
            <td>x<?= e($item['cantidad']) ?></td>
            <td>S/ <?= e(number_format((float) $item['precio_unitario'], 2)) ?></td>
            <td>S/ <?= e(number_format((float) $item['subtotal'], 2)) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p><strong>Costo de envío:</strong> S/ <?= e(number_format((float) $orden['costo_envio'], 2)) ?></p>
<p><strong>Total:</strong> S/ <?= e(number_format((float) $orden['total'], 2)) ?></p>
