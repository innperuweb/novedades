<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Información del sitio</h2>
            <p class="text-muted mb-0">Gestiona los datos de contacto, redes sociales y el mensaje del header.</p>
        </div>
        <a class="btn btn-primary" href="<?= base_url('admin/informacion/editar/contacto'); ?>">Editar información</a>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">Contacto</h5>
                            <p class="text-muted small mb-0">Teléfonos y correo del footer.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/informacion/editar/contacto'); ?>">Editar</a>
                    </div>
                    <ul class="list-unstyled mb-0 small">
                        <li><strong>Teléfono 1:</strong> <?= e($informacion['contacto']['telefono1'] ?? ''); ?></li>
                        <li><strong>Teléfono 2:</strong> <?= e($informacion['contacto']['telefono2'] ?? ''); ?></li>
                        <li><strong>Email:</strong> <?= e($informacion['contacto']['email'] ?? ''); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">Redes sociales</h5>
                            <p class="text-muted small mb-0">Enlaces del footer.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/informacion/editar/redes'); ?>">Editar</a>
                    </div>
                    <ul class="list-unstyled mb-0 small">
                        <li><strong>Facebook:</strong> <?= e($informacion['redes']['facebook'] ?? ''); ?></li>
                        <li><strong>Instagram:</strong> <?= e($informacion['redes']['instagram'] ?? ''); ?></li>
                        <li><strong>YouTube:</strong> <?= e($informacion['redes']['youtube'] ?? ''); ?></li>
                        <li><strong>TikTok:</strong> <?= e($informacion['redes']['tiktok'] ?? ''); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1">Mensaje del header</h5>
                            <p class="text-muted small mb-0">Texto de la barra superior.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/informacion/editar/header'); ?>">Editar</a>
                    </div>
                    <p class="mb-0 small"><?= e($informacion['header']['mensaje_header'] ?? ''); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
