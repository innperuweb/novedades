<?php
    $formAction = $esEdicion ? base_url('admin/usuarios/actualizar/' . (int) ($usuario['id'] ?? 0)) : base_url('admin/usuarios/guardar');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0"><?= $esEdicion ? 'Editar usuario' : 'Nuevo usuario'; ?></h1>
    <a class="btn btn-outline-secondary" href="<?= base_url('admin/usuarios'); ?>">Volver</a>
</div>

<form action="<?= $formAction; ?>" method="post" class="row g-4">
    <?= csrf_field(); ?>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : ''; ?>" value="<?= e($usuario['nombre'] ?? ''); ?>" required>
                    <?php if (isset($errores['nombre'])): ?>
                        <div class="invalid-feedback"><?= e($errores['nombre']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control <?= isset($errores['email']) ? 'is-invalid' : ''; ?>" value="<?= e($usuario['email'] ?? ''); ?>" required>
                    <?php if (isset($errores['email'])): ?>
                        <div class="invalid-feedback"><?= e($errores['email']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña<?= $esEdicion ? ' (dejar en blanco para mantener)' : ''; ?></label>
                        <input type="password" name="password" id="password" class="form-control <?= isset($errores['password']) ? 'is-invalid' : ''; ?>">
                        <?php if (!$esEdicion): ?>
                            <div class="form-hint">Debe tener al menos 8 caracteres.</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control <?= isset($errores['password']) ? 'is-invalid' : ''; ?>">
                        <?php if (isset($errores['password'])): ?>
                            <div class="invalid-feedback d-block"><?= e($errores['password']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select name="rol" id="rol" class="form-select <?= isset($errores['rol']) ? 'is-invalid' : ''; ?>" required>
                        <?php foreach ($roles as $valor => $etiqueta): ?>
                            <option value="<?= e($valor); ?>" <?= ($usuario['rol'] ?? 'admin') === $valor ? 'selected' : ''; ?>><?= e($etiqueta); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['rol'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['rol']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" <?= (int) ($usuario['activo'] ?? 1) === 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="activo">Usuario activo</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><?= $esEdicion ? 'Guardar cambios' : 'Crear usuario'; ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
