<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Orden <?= e($orden['nro_orden'] ?? ''); ?></h1>
        <p class="text-muted mb-0">Fecha: <?= e(date('d/m/Y H:i', strtotime((string) ($orden['fecha'] ?? 'now')))); ?></p>
    </div>
    <div>
        <form action="<?= base_url('admin/ordenes/estado'); ?>" method="post" class="d-flex align-items-center gap-2">
            <?= csrf_field(); ?>
            <input type="hidden" name="orden_id" value="<?= (int) ($orden['id'] ?? 0); ?>">
            <input type="hidden" name="redirect_to" value="<?= e(current_url()); ?>">
            <select name="estado" class="form-select">
                <?php foreach ($estados as $estadoItem): ?>
                    <option value="<?= e($estadoItem); ?>" <?= ($orden['estado'] ?? '') === $estadoItem ? 'selected' : ''; ?>><?= e($estadoItem); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Actualizar estado</button>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Cliente</h2>
                <p class="mb-1 fw-semibold"><?= e(trim(($orden['cliente_nombre'] ?? '') . ' ' . ($orden['cliente_apellidos'] ?? ''))); ?></p>
                <p class="mb-1">Correo: <a href="mailto:<?= e($orden['cliente_email'] ?? ''); ?>"><?= e($orden['cliente_email'] ?? ''); ?></a></p>
                <p class="mb-1">Teléfono: <?= e($orden['cliente_telefono'] ?? $orden['telefono'] ?? ''); ?></p>
                <p class="mb-0">Dirección: <?= e($orden['cliente_direccion'] ?? $orden['direccion'] ?? ''); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Pago y envío</h2>
                <p class="mb-1">Método de pago: <?= e($orden['metodo_pago'] ?? ''); ?></p>
                <p class="mb-1">Método de envío: <?= e($orden['metodo_envio_texto'] ?? $orden['metodo_envio'] ?? ''); ?></p>
                <p class="mb-1">Distrito: <?= e($orden['cliente_distrito'] ?? $orden['distrito'] ?? ''); ?></p>
                <p class="mb-0">Referencia: <?= e($orden['referencia'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orden['detalle'])): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">La orden no tiene productos asociados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orden['detalle'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($item['nombre_producto'] ?? $item['nombre'] ?? ''); ?></div>
                                    <div class="text-muted small">Color: <?= e($item['color'] ?? '-'); ?> | Talla: <?= e($item['talla'] ?? '-'); ?></div>
                                </td>
                                <td class="text-center"><?= (int) ($item['cantidad'] ?? 0); ?></td>
                                <td>S/ <?= number_format((float) ($item['precio_unitario'] ?? 0), 2); ?></td>
                                <td>S/ <?= number_format((float) ($item['subtotal'] ?? 0), 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Subtotal</th>
                        <th>S/ <?= number_format((float) ($orden['subtotal'] ?? 0), 2); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Envío</th>
                        <th>S/ <?= number_format((float) ($orden['costo_envio'] ?? 0), 2); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th>S/ <?= number_format((float) ($orden['total'] ?? 0), 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
