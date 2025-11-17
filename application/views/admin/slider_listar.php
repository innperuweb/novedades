<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Slider</h1>
    <a class="btn btn-primary" href="<?= base_url('admin/slider/crear'); ?>">Nuevo slider</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle admin-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Imagen</th>
                        <th>Título</th>
                        <th style="width: 100px;">Orden</th>
                        <th style="width: 100px;">Visible</th>
                        <th class="text-end" style="width: 160px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sliders === []): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay sliders registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sliders as $slider): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($slider['imagen'])): ?>
                                        <img src="<?= asset_url($slider['imagen']); ?>" alt="Imagen del slider" class="img-thumbnail" style="max-width: 120px; height: auto;">
                                    <?php else: ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $titulo = strip_tags((string) ($slider['titulo'] ?? ''));
                                    $resumido = mb_strimwidth($titulo, 0, 80, '...');
                                    ?>
                                    <div class="fw-semibold"><?= e($resumido); ?></div>
                                </td>
                                <td><?= (int) ($slider['orden'] ?? 0); ?></td>
                                <td>
                                    <?php if ((int) ($slider['visible'] ?? 0) === 1): ?>
                                        <span class="badge bg-success-subtle text-success">SI</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">NO</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/slider/editar/' . (int) ($slider['id'] ?? 0)); ?>">Editar</a>
                                        <form action="<?= base_url('admin/slider/eliminar/' . (int) ($slider['id'] ?? 0)); ?>" method="post" onsubmit="return confirm('¿Eliminar slider?');">
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
