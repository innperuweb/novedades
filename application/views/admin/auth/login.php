<div class="container">
    <div class="card admin-auth-card">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-3 fw-bold text-center">Panel administrativo</h1>
            <p class="text-center text-muted mb-4">Ingresa tus credenciales para continuar.</p>
            <?php if (!empty($flash['message'])): ?>
                <div class="alert alert-<?= e($flash['type'] ?? 'info'); ?>">
                    <?= e($flash['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="<?= base_url('admin/login'); ?>" method="post" class="needs-validation" novalidate>
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= e($email ?? ''); ?>" required autocomplete="email" autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </div>
            </form>
        </div>
    </div>
</div>
