<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Experiencias</h1>
    <a class="btn btn-primary" href="<?= base_url('admin/experiencias/crear'); ?>">Nueva experiencia</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Nombre</th>
                        <th style="width: 120px;">Imagen</th>
                        <th>Texto</th>
                        <th style="width: 110px;">Estado</th>
                        <th class="text-end" style="width: 180px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($experiencias === []): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay experiencias registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($experiencias as $experiencia): ?>
                            <tr>
                                <td class="fw-semibold">#<?= (int) ($experiencia['id'] ?? 0); ?></td>
                                <td><?= e($experiencia['nombre'] ?? ''); ?></td>
                                <td>
                                    <?php if (!empty($experiencia['imagen'])): ?>
                                        <img src="<?= asset_url('uploads/experiencias/' . $experiencia['imagen']); ?>" alt="Imagen de <?= e($experiencia['nombre'] ?? ''); ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $texto = strip_tags((string) ($experiencia['texto'] ?? ''));
                                    $resumen = mb_strimwidth($texto, 0, 100, '...');
                                    ?>
                                    <span class="text-muted small"><?= e($resumen); ?></span>
                                </td>
                                <td>
                                    <?php if ((int) ($experiencia['visible'] ?? 0) === 1): ?>
                                        <span class="badge bg-success-subtle text-success">Visible</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No visible</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/experiencias/editar/' . (int) ($experiencia['id'] ?? 0)); ?>">Editar</a>
                                        <form action="<?= base_url('admin/experiencias/eliminar/' . (int) ($experiencia['id'] ?? 0)); ?>" method="post" onsubmit="return confirm('¿Eliminar experiencia?');">
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
