<?php
    $registro = $publicidad ?? [];
    $errores = $errores ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Publicidad</h1>
</div>

<form action="<?= base_url('admin/publicidad/guardar'); ?>" method="post" enctype="multipart/form-data" class="row g-4">
    <?= csrf_field(); ?>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen del banner</label>
                    <?php if (!empty($registro['imagen'])): ?>
                        <div class="mb-2">
                            <img src="<?= base_url($registro['imagen']); ?>" alt="Imagen actual" class="img-thumbnail" style="max-width: 320px; height: auto;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp" class="form-control <?= isset($errores['imagen']) ? 'is-invalid' : ''; ?>">
                    <div class="form-text">Formatos permitidos: JPG, JPEG, PNG, WEBP.</div>
                    <?php if (isset($errores['imagen'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['imagen']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Texto superior</label>
                    <input type="text" name="titulo" id="titulo" class="form-control <?= isset($errores['titulo']) ? 'is-invalid' : ''; ?>" value="<?= e($registro['titulo'] ?? ''); ?>" required>
                    <?php if (isset($errores['titulo'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['titulo']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="subtitulo" class="form-label">Texto principal destacado</label>
                    <input type="text" name="subtitulo" id="subtitulo" class="form-control <?= isset($errores['subtitulo']) ? 'is-invalid' : ''; ?>" value="<?= e($registro['subtitulo'] ?? ''); ?>" required>
                    <?php if (isset($errores['subtitulo'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['subtitulo']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="texto" class="form-label">Texto inferior</label>
                    <input type="text" name="texto" id="texto" class="form-control <?= isset($errores['texto']) ? 'is-invalid' : ''; ?>" value="<?= e($registro['texto'] ?? ''); ?>" required>
                    <?php if (isset($errores['texto'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['texto']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
</form>
