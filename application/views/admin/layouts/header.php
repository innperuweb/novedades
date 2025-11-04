<header class="admin-header navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 brand-title">Panel administrativo</span>
        <div class="d-flex align-items-center gap-3">
            <?php if (!empty($currentUser)): ?>
                <div class="text-white-50 text-end">
                    <div class="fw-semibold text-white"><?= e($currentUser['nombre'] ?? ''); ?></div>
                    <small><?= e($currentUser['email'] ?? ''); ?></small>
                </div>
            <?php endif; ?>
            <form action="<?= base_url('admin/logout'); ?>" method="post" class="m-0">
                <?= csrf_field(); ?>
                <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
            </form>
        </div>
    </div>
</header>
<div class="admin-body d-flex flex-grow-1">
