<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Productos</h1>
    <a class="btn btn-primary" href="<?= base_url('admin/productos/crear'); ?>">Nuevo producto</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categorías</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($productos === []): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay productos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold mb-1"><?= e($producto['nombre'] ?? ''); ?></div>
                                    <div class="text-muted small">SKU: <?= e($producto['sku'] ?? ''); ?></div>
                                </td>
                                <td>S/ <?= number_format((float) ($producto['precio'] ?? 0), 2); ?></td>
                                <td><?= (int) ($producto['stock'] ?? 0); ?></td>
                                <td>
                                    <?php if (!empty($producto['subcategorias_nombres'])): ?>
                                        <?php foreach ($producto['subcategorias_nombres'] as $nombre): ?>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis badge-pill me-1 mb-1"><?= e($nombre); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int) ($producto['activo'] ?? 0) === 1): ?>
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group" aria-label="Acciones">
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/productos/editar/' . (int) ($producto['id'] ?? 0)); ?>">Editar</a>
                                        <form action="<?= base_url('admin/productos/eliminar/' . (int) ($producto['id'] ?? 0)); ?>" method="post" onsubmit="return confirm('¿Eliminar producto?');">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
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
