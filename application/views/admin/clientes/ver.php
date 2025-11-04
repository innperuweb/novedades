<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1"><?= e(trim(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? ''))); ?></h1>
        <p class="text-muted mb-0">Registrado el <?= isset($cliente['fecha_registro']) ? e(date('d/m/Y H:i', strtotime((string) $cliente['fecha_registro']))) : '-'; ?></p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= base_url('admin/clientes'); ?>">Volver</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Contacto</h2>
                <p class="mb-1">Correo: <a href="mailto:<?= e($cliente['email'] ?? ''); ?>"><?= e($cliente['email'] ?? ''); ?></a></p>
                <p class="mb-1">Teléfono: <?= e($cliente['telefono'] ?? ''); ?></p>
                <p class="mb-0">Dirección: <?= e($cliente['direccion'] ?? ''); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Información adicional</h2>
                <p class="mb-1">Distrito: <?= e($cliente['distrito'] ?? ''); ?></p>
                <p class="mb-1">Referencia: <?= e($cliente['referencia'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Órdenes del cliente</h2>
        <span class="badge bg-secondary-subtle text-secondary-emphasis"><?= count($cliente['ordenes'] ?? []); ?> registradas</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th># Orden</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cliente['ordenes'])): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">El cliente aún no tiene órdenes.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cliente['ordenes'] as $orden): ?>
                            <tr>
                                <td class="fw-semibold"><?= e($orden['nro_orden'] ?? ''); ?></td>
                                <td><?= e(date('d/m/Y H:i', strtotime((string) ($orden['fecha'] ?? 'now')))); ?></td>
                                <td>S/ <?= number_format((float) ($orden['total'] ?? 0), 2); ?></td>
                                <td><span class="status-badge" data-status="<?= e($orden['estado'] ?? ''); ?>"><?= e($orden['estado'] ?? ''); ?></span></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/ordenes/ver/' . (int) ($orden['id'] ?? 0)); ?>">Ver orden</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
