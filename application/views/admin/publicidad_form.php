<?php
    $publicidades = $publicidades ?? [];
    $errores = $errores ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Publicidad</h1>
</div>

<form action="<?= base_url('admin/publicidad/guardar'); ?>" method="post" enctype="multipart/form-data" class="row g-4">
    <?= csrf_field(); ?>
    <div class="col-lg-8">

        <?php for ($posicion = 1; $posicion <= 4; $posicion++): ?>
            <?php $registro = $publicidades[$posicion] ?? []; ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-4">Banner <?= $posicion; ?></h2>

                    <div class="mb-3">
                        <label for="imagen-<?= $posicion; ?>" class="form-label">Imagen del banner</label>

                        <?php if (!empty($registro['imagen'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url($registro['imagen']); ?>" alt="Imagen actual"
                                     class="img-thumbnail" style="max-width: 320px; height: auto;">
                            </div>
                        <?php endif; ?>

                        <input type="file" name="imagen[<?= $posicion; ?>]" id="imagen-<?= $posicion; ?>"
                               accept=".jpg,.jpeg,.png,.webp"
                               class="form-control <?= isset($errores[$posicion]['imagen']) ? 'is-invalid' : ''; ?>">
                        <div class="form-text">Formatos permitidos: JPG, JPEG, PNG, WEBP.</div>

                        <?php if (isset($errores[$posicion]['imagen'])): ?>
                            <div class="invalid-feedback d-block"><?= e($errores[$posicion]['imagen']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="titulo-<?= $posicion; ?>" class="form-label">Texto superior</label>
                        <input type="text" name="titulo[<?= $posicion; ?>]" id="titulo-<?= $posicion; ?>"
                               class="form-control <?= isset($errores[$posicion]['titulo']) ? 'is-invalid' : ''; ?>"
                               value="<?= e($registro['titulo'] ?? ''); ?>" required>
                        <?php if (isset($errores[$posicion]['titulo'])): ?>
                            <div class="invalid-feedback d-block"><?= e($errores[$posicion]['titulo']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="subtitulo-<?= $posicion; ?>" class="form-label">Texto principal destacado</label>
                        <input type="text" name="subtitulo[<?= $posicion; ?>]" id="subtitulo-<?= $posicion; ?>"
                               class="form-control <?= isset($errores[$posicion]['subtitulo']) ? 'is-invalid' : ''; ?>"
                               value="<?= e($registro['subtitulo'] ?? ''); ?>" required>
                        <?php if (isset($errores[$posicion]['subtitulo'])): ?>
                            <div class="invalid-feedback d-block"><?= e($errores[$posicion]['subtitulo']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="texto-<?= $posicion; ?>" class="form-label">Texto inferior</label>
                        <input type="text" name="texto[<?= $posicion; ?>]" id="texto-<?= $posicion; ?>"
                               class="form-control <?= isset($errores[$posicion]['texto']) ? 'is-invalid' : ''; ?>"
                               value="<?= e($registro['texto'] ?? ''); ?>" required>
                        <?php if (isset($errores[$posicion]['texto'])): ?>
                            <div class="invalid-feedback d-block"><?= e($errores[$posicion]['texto']); ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endfor; ?>

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