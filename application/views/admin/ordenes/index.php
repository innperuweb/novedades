<div class="d-flex flex-column flex-lg-row flex-lg-wrap gap-3 align-items-lg-end justify-content-between mb-4">
    <form class="row gy-2 gx-2 align-items-end" method="get" action="<?= base_url('admin/ordenes'); ?>">
        <div class="col-auto">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($estados as $estadoItem): ?>
                    <option value="<?= e($estadoItem); ?>" <?= $estadoSeleccionado === $estadoItem ? 'selected' : ''; ?>><?= e($estadoItem); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label for="q" class="form-label">Buscar</label>
            <input type="text" name="q" id="q" class="form-control" value="<?= e($busqueda); ?>" placeholder="Nro, cliente o email">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?= base_url('admin/ordenes'); ?>" class="btn btn-link text-decoration-none">Limpiar</a>
        </div>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Correo</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ordenes === []): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No se encontraron órdenes.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ordenes as $orden): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <a href="<?= base_url('admin/ordenes/ver/' . (int) ($orden['id'] ?? 0)); ?>">
                                        <?= e($orden['nro_orden'] ?? ''); ?>
                                    </a>
                                </td>
                                <td><?= e(trim(($orden['cliente_nombre'] ?? '') . ' ' . ($orden['cliente_apellidos'] ?? ''))); ?></td>
                                <td><?= e($orden['cliente_email'] ?? ''); ?></td>
                                <td><?= e(date('d/m/Y H:i', strtotime((string) ($orden['fecha'] ?? 'now')))); ?></td>
                                <td>S/ <?= number_format((float) ($orden['total'] ?? 0), 2); ?></td>
                                <td>
                                    <span class="status-badge" data-status="<?= e($orden['estado'] ?? ''); ?>"><?= e($orden['estado'] ?? ''); ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/ordenes/ver/' . (int) ($orden['id'] ?? 0)); ?>">Ver</a>
                                        <form action="<?= base_url('admin/ordenes/estado'); ?>" method="post" class="d-flex align-items-center gap-2">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="orden_id" value="<?= (int) ($orden['id'] ?? 0); ?>">
                                            <input type="hidden" name="redirect_to" value="<?= e(current_url()); ?>">
                                            <select name="estado" class="form-select form-select-sm">
                                                <?php foreach ($estados as $estadoItem): ?>
                                                    <option value="<?= e($estadoItem); ?>" <?= ($orden['estado'] ?? '') === $estadoItem ? 'selected' : ''; ?>><?= e($estadoItem); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
