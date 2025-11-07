<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/OrdenModel.php';

$id_cliente = isset($_SESSION['id_cliente']) ? (int) $_SESSION['id_cliente'] : null;
$ordenes = $ordenes ?? ($id_cliente ? OrdenModel::obtenerPorCliente($id_cliente) : []);
?>

<div id="content" class="main-content-wrapper ptb--80">
    <div class="page-content-inner">
        <main class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 mb--30 mb-lg--0">
                        <div class="nav flex-column nav-pills" id="account-sidebar" role="tablist">
                            <a class="nav-link active" href="<?= e(base_url('mi_cuenta')); ?>">Órdenes</a>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="tab-content" id="account-content">
                            <div class="tab-pane fade show active" id="ordenes" role="tabpanel">
                                <h2 class="mb--30">Mis órdenes</h2>
                                <div class="myaccount-table table-responsive text-center">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>N° de orden</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                                <th>Total</th>
                                                <th>Ver</th>
                                                <th>Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($ordenes)) : ?>
                                                <?php foreach ($ordenes as $orden) : ?>
                                                    <tr>
                                                        <td><?= e($orden['nro_orden'] ?? '') ?></td>
                                                        <td>
                                                            <?php
                                                            $fecha = $orden['fecha'] ?? '';
                                                            $timestamp = $fecha !== '' ? strtotime((string) $fecha) : false;
                                                            echo $timestamp !== false ? e(date('d/m/Y', (int) $timestamp)) : '';
                                                            ?>
                                                        </td>
                                                        <td><?= e($orden['estado'] ?? '') ?></td>
                                                        <td>S/ <?= number_format((float) ($orden['total'] ?? 0), 2) ?></td>
                                                        <td>
                                                            <a href="<?= e(base_url('ver_orden?nro=' . urlencode((string) ($orden['nro_orden'] ?? '')))) ?>">Ver</a>
                                                        </td>
                                                        <td>
                                                            <a href="#" class="text-danger eliminar-orden" data-id="<?= e((string) ($orden['id'] ?? '')) ?>" data-nro="<?= e((string) ($orden['nro_orden'] ?? '')) ?>">×</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="6">No tienes órdenes registradas.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.querySelectorAll('.eliminar-orden').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const id = btn.dataset.id;
            const nro = btn.dataset.nro;
            if (confirm('¿Eliminar esta orden?')) {
                const baseUrl = <?= json_encode(base_url('mi_cuenta/eliminar')); ?>;
                const query = id ? `id=${encodeURIComponent(id)}` : (nro ? `nro=${encodeURIComponent(nro)}` : '');
                window.location.href = query ? `${baseUrl}?${query}` : baseUrl;
            }
        });
    });
</script>
