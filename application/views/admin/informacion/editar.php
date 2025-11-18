<?php
$activeTab = $activeTab ?? 'contacto';
$infoContacto = $informacion['contacto'] ?? [];
$infoRedes = $informacion['redes'] ?? [];
$infoHeader = $informacion['header'] ?? [];
$errores = $errores ?? [];
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Editar información</h2>
            <p class="text-muted mb-0">Actualiza el contacto, las redes sociales y el mensaje del header.</p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= base_url('admin/informacion'); ?>">Volver</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <ul class="nav nav-tabs" id="informacionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'contacto' ? 'active' : ''; ?>" id="contacto-tab"
                        data-bs-toggle="tab" data-bs-target="#contacto" type="button" role="tab" aria-controls="contacto"
                        aria-selected="<?= $activeTab === 'contacto' ? 'true' : 'false'; ?>">Contacto</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'redes' ? 'active' : ''; ?>" id="redes-tab" data-bs-toggle="tab"
                        data-bs-target="#redes" type="button" role="tab" aria-controls="redes"
                        aria-selected="<?= $activeTab === 'redes' ? 'true' : 'false'; ?>">Redes Sociales</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'header' ? 'active' : ''; ?>" id="header-tab"
                        data-bs-toggle="tab" data-bs-target="#header" type="button" role="tab" aria-controls="header"
                        aria-selected="<?= $activeTab === 'header' ? 'true' : 'false'; ?>">Mensaje Header</button>
                </li>
            </ul>
            <div class="tab-content pt-3" id="informacionTabsContent">
                <div class="tab-pane fade <?= $activeTab === 'contacto' ? 'show active' : ''; ?>" id="contacto" role="tabpanel"
                    aria-labelledby="contacto-tab">
                    <form method="post" action="<?= base_url('admin/informacion/editar/contacto'); ?>">
                        <?= csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="telefono1">Teléfono 1</label>
                            <input type="text" class="form-control" id="telefono1" name="telefono1"
                                value="<?= e($infoContacto['telefono1'] ?? ''); ?>" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="telefono2">Teléfono 2</label>
                            <input type="text" class="form-control" id="telefono2" name="telefono2"
                                value="<?= e($infoContacto['telefono2'] ?? ''); ?>" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="email">Email</label>
                            <input type="email" class="form-control <?= isset($errores['email']) ? 'is-invalid' : ''; ?>" id="email"
                                name="email" value="<?= e($infoContacto['email'] ?? ''); ?>" maxlength="100">
                            <?php if (isset($errores['email'])): ?>
                                <div class="invalid-feedback"><?= e($errores['email']); ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>
                </div>

                <div class="tab-pane fade <?= $activeTab === 'redes' ? 'show active' : ''; ?>" id="redes" role="tabpanel"
                    aria-labelledby="redes-tab">
                    <form method="post" action="<?= base_url('admin/informacion/editar/redes'); ?>">
                        <?= csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="facebook">Facebook</label>
                            <input type="url" class="form-control <?= isset($errores['facebook']) ? 'is-invalid' : ''; ?>"
                                id="facebook" name="facebook" value="<?= e($infoRedes['facebook'] ?? ''); ?>" maxlength="255"
                                placeholder="https://facebook.com/usuario">
                            <?php if (isset($errores['facebook'])): ?>
                                <div class="invalid-feedback"><?= e($errores['facebook']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="instagram">Instagram</label>
                            <input type="url" class="form-control <?= isset($errores['instagram']) ? 'is-invalid' : ''; ?>"
                                id="instagram" name="instagram" value="<?= e($infoRedes['instagram'] ?? ''); ?>" maxlength="255"
                                placeholder="https://instagram.com/usuario">
                            <?php if (isset($errores['instagram'])): ?>
                                <div class="invalid-feedback"><?= e($errores['instagram']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="youtube">YouTube</label>
                            <input type="url" class="form-control <?= isset($errores['youtube']) ? 'is-invalid' : ''; ?>" id="youtube"
                                name="youtube" value="<?= e($infoRedes['youtube'] ?? ''); ?>" maxlength="255"
                                placeholder="https://youtube.com/usuario">
                            <?php if (isset($errores['youtube'])): ?>
                                <div class="invalid-feedback"><?= e($errores['youtube']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="tiktok">TikTok</label>
                            <input type="url" class="form-control <?= isset($errores['tiktok']) ? 'is-invalid' : ''; ?>" id="tiktok"
                                name="tiktok" value="<?= e($infoRedes['tiktok'] ?? ''); ?>" maxlength="255"
                                placeholder="https://www.tiktok.com/@usuario">
                            <?php if (isset($errores['tiktok'])): ?>
                                <div class="invalid-feedback"><?= e($errores['tiktok']); ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>
                </div>

                <div class="tab-pane fade <?= $activeTab === 'header' ? 'show active' : ''; ?>" id="header" role="tabpanel"
                    aria-labelledby="header-tab">
                    <form method="post" action="<?= base_url('admin/informacion/editar/header'); ?>">
                        <?= csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="mensaje_header">Mensaje</label>
                            <input type="text" class="form-control" id="mensaje_header" name="mensaje_header"
                                value="<?= e($infoHeader['mensaje_header'] ?? ''); ?>" maxlength="255">
                            <div class="form-text">Texto mostrado en la barra superior del header.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
