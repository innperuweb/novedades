<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Usuarios administrativos</h1>
    <a class="btn btn-primary" href="<?= base_url('admin/usuarios/crear'); ?>">Nuevo usuario</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($usuarios === []): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td class="fw-semibold"><?= e($usuario['nombre'] ?? ''); ?></td>
                                <td><?= e($usuario['email'] ?? ''); ?></td>
                                <td><?= e(ucfirst($usuario['rol'] ?? '')); ?></td>
                                <td>
                                    <?php if ((int) ($usuario['activo'] ?? 0) === 1): ?>
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($usuario['ultimo_acceso'])): ?>
                                        <?= e(date('d/m/Y H:i', strtotime((string) $usuario['ultimo_acceso']))); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/usuarios/editar/' . (int) ($usuario['id'] ?? 0)); ?>">Editar</a>
                                        <form action="<?= base_url('admin/usuarios/eliminar/' . (int) ($usuario['id'] ?? 0)); ?>" method="post" onsubmit="return confirm('¿Eliminar usuario?');">
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
