<?php
    $formAction = $esEdicion ? base_url('admin/slider/actualizar/' . (int) ($slider['id'] ?? 0)) : base_url('admin/slider/guardar');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0"><?= $esEdicion ? 'Editar slider' : 'Nuevo slider'; ?></h1>
    <a class="btn btn-outline-secondary" href="<?= base_url('admin/slider'); ?>">Volver</a>
</div>

<form action="<?= $formAction; ?>" method="post" enctype="multipart/form-data" class="row g-4">
    <?= csrf_field(); ?>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <?php if (!empty($slider['imagen'])): ?>
                        <div class="mb-2">
                            <img src="<?= asset_url($slider['imagen']); ?>" alt="Imagen actual" class="img-thumbnail" style="max-width: 240px; height: auto;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp" class="form-control <?= isset($errores['imagen']) ? 'is-invalid' : ''; ?>">
                    <div class="form-text">Formatos permitidos: JPG, JPEG, PNG, WEBP.</div>
                    <?php if (isset($errores['imagen'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['imagen']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <textarea name="titulo" id="titulo" class="form-control summernote <?= isset($errores['titulo']) ? 'is-invalid' : ''; ?>" rows="3" required><?= e($slider['titulo'] ?? ''); ?></textarea>
                    <?php if (isset($errores['titulo'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['titulo']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="subtitulo" class="form-label">Subtítulo</label>
                    <textarea name="subtitulo" id="subtitulo" class="form-control summernote" rows="3"><?= e($slider['subtitulo'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3">
                    <label for="boton_texto" class="form-label">Texto del botón</label>
                    <input type="text" name="boton_texto" id="boton_texto" class="form-control" value="<?= e($slider['boton_texto'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="boton_url" class="form-label">URL del botón</label>
                    <input type="text" name="boton_url" id="boton_url" class="form-control" placeholder="productos/detalle?id=25" value="<?= e($slider['boton_url'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="orden" class="form-label">Orden</label>
                    <input type="number" name="orden" id="orden" class="form-control" value="<?= (int) ($slider['orden'] ?? 0); ?>">
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="visible" name="visible" value="1" <?= (int) ($slider['visible'] ?? 1) === 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="visible">Visible en la web</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><?= $esEdicion ? 'Guardar cambios' : 'Crear slider'; ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3gJwYp80bHDr3jsPZp+y5A1u3xj1H1P9AX1aDYZl4Ps=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.summernote').summernote({
            placeholder: '',
            tabsize: 2,
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    });
</script>
