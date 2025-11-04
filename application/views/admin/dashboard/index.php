<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <span class="text-uppercase text-muted fw-semibold small">Ventas totales</span>
                <h3 class="mt-2 mb-0">S/ <?= number_format((float) $ventasTotales, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <span class="text-uppercase text-muted fw-semibold small">Órdenes pendientes</span>
                <h3 class="mt-2 mb-0"><?= (int) $ordenesPendientes; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <span class="text-uppercase text-muted fw-semibold small">Clientes</span>
                <h3 class="mt-2 mb-0"><?= (int) $totalClientes; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <span class="text-uppercase text-muted fw-semibold small">Productos</span>
                <h3 class="mt-2 mb-0"><?= (int) $totalProductos; ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Últimas órdenes</h2>
        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/ordenes'); ?>">Ver todas</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table">
                <thead>
                    <tr>
                        <th># Orden</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ultimasOrdenes === []): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay órdenes registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimasOrdenes as $orden): ?>
                            <tr>
                                <td class="fw-semibold"><?= e($orden['nro_orden'] ?? ''); ?></td>
                                <td><?= e(trim(($orden['cliente_nombre'] ?? '') . ' ' . ($orden['cliente_apellidos'] ?? ''))); ?></td>
                                <td><?= e(date('d/m/Y H:i', strtotime((string) ($orden['fecha'] ?? 'now')))); ?></td>
                                <td>S/ <?= number_format((float) ($orden['total'] ?? 0), 2); ?></td>
                                <td><span class="status-badge" data-status="<?= e($orden['estado'] ?? ''); ?>"><?= e($orden['estado'] ?? ''); ?></span></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/ordenes/ver/' . (int) ($orden['id'] ?? 0)); ?>">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
