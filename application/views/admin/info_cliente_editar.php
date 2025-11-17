<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Información para el cliente</h2>
            <p class="text-muted mb-0">Editando: <?= e($titulo); ?></p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= base_url('admin/dashboard'); ?>">Volver al panel</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="slug" value="<?= e($slug); ?>">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Título</label>
                    <input type="text" class="form-control" value="<?= e($titulo); ?>" disabled>
                    <div class="form-text">El título se determina automáticamente según la sección seleccionada.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="editor">Contenido</label>
                    <textarea id="editor" name="contenido"><?= $contenido; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<link rel="stylesheet" href="<?= asset_url('summernote/summernote-lite.min.css'); ?>">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script src="<?= asset_url('summernote/summernote-lite.min.js'); ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.jQuery === 'undefined') {
            console.error('jQuery es requerido para cargar el editor.');
            return;
        }

        $('#editor').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
