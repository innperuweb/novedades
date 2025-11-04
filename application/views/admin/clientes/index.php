<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Clientes</h1>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Órdenes</th>
                        <th>Última compra</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clientes === []): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay clientes registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="fw-semibold"><?= e(trim(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? ''))); ?></td>
                                <td><?= e($cliente['email'] ?? ''); ?></td>
                                <td><?= e($cliente['telefono'] ?? ''); ?></td>
                                <td><?= (int) ($cliente['total_ordenes'] ?? 0); ?></td>
                                <td>
                                    <?php if (!empty($cliente['ultima_compra'])): ?>
                                        <?= e(date('d/m/Y H:i', strtotime((string) $cliente['ultima_compra']))); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/clientes/ver/' . (int) ($cliente['id'] ?? 0)); ?>">Ver detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
