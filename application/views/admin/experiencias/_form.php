<?php
$formAction = $esEdicion
    ? base_url('admin/experiencias/editar/' . (int) ($experiencia['id'] ?? 0))
    : base_url('admin/experiencias/crear');
?>

<form action="<?= $formAction; ?>" method="post" enctype="multipart/form-data" class="row g-4">
    <?= csrf_field(); ?>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : ''; ?>" value="<?= e($experiencia['nombre'] ?? ''); ?>" required>
                    <?php if (isset($errores['nombre'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['nombre']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="texto" class="form-label">Texto</label>
                    <textarea name="texto" id="texto" class="form-control summernote <?= isset($errores['texto']) ? 'is-invalid' : ''; ?>" rows="4" required><?= e($experiencia['texto'] ?? ''); ?></textarea>
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
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <?php if (!empty($experiencia['imagen'])): ?>
                        <div class="mb-2">
                            <img src="<?= asset_url('uploads/experiencias/' . $experiencia['imagen']); ?>" alt="Imagen actual" class="img-thumbnail" style="max-width: 160px; height: auto;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp" class="form-control <?= isset($errores['imagen']) ? 'is-invalid' : ''; ?>">
                    <div class="form-text">Formatos permitidos: JPG, JPEG, PNG, WEBP. Máx 2MB.</div>
                    <?php if (isset($errores['imagen'])): ?>
                        <div class="invalid-feedback d-block"><?= e($errores['imagen']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="visible" name="visible" value="1" <?= (int) ($experiencia['visible'] ?? 1) === 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="visible">Visible en la web</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
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
            height: 180,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    });
</script>
