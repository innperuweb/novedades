<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/OrdenModel.php';

$id_cliente = isset($_SESSION['id_cliente']) ? (int) $_SESSION['id_cliente'] : null;
$ordenes = $ordenes ?? ($id_cliente ? OrdenModel::obtenerPorCliente($id_cliente) : []);
?>

<h2>Mis órdenes</h2>

<?php if ($ordenes === []): ?>
    <p>No tienes órdenes registradas.</p>
<?php else: ?>
    <table>
        <tr>
            <th>N° de orden</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Ver</th>
            <th>Eliminar</th>
        </tr>
        <?php foreach ($ordenes as $orden): ?>
            <tr>
                <td><?= e($orden['nro_orden']) ?></td>
                <td><?= e(date('d/m/Y', strtotime($orden['fecha']))) ?></td>
                <td><?= e($orden['estado']) ?></td>
                <td>S/ <?= e(number_format((float) $orden['total'], 2)) ?></td>
                <td><a href="<?= e(base_url('ver_orden?nro=' . urlencode($orden['nro_orden']))) ?>">Ver</a></td>
                <td>
                    <a href="<?= e(base_url('mi_cuenta/eliminar?id=' . urlencode((string) $orden['id']))) ?>"
                       onclick="return confirm('¿Eliminar esta orden?')">X</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
