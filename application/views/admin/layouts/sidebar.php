<?php
$requestPath = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$requestPath = preg_replace('#^admin/?#', '', $requestPath ?? '') ?? '';
$isActive = static function (array $paths) use ($requestPath): bool {
    foreach ($paths as $path) {
        $normalized = trim($path, '/');
        if ($normalized === '' && $requestPath === '') {
            return true;
        }

        if ($normalized !== '' && strpos($requestPath, $normalized) === 0) {
            return true;
        }
    }

    return false;
};
?>
<aside class="admin-sidebar p-4 d-flex flex-column">
    <nav class="nav nav-pills flex-column">

        <a class="nav-link <?= $isActive(['', 'dashboard']) ? 'active' : ''; ?>" href="<?= base_url('admin/dashboard'); ?>">Dashboard</a>
        <a class="nav-link <?= $isActive(['ordenes']) ? 'active' : ''; ?>" href="<?= base_url('admin/ordenes'); ?>">Órdenes</a>
        <a class="nav-link <?= $isActive(['productos']) ? 'active' : ''; ?>" href="<?= base_url('admin/productos'); ?>">Productos</a>
        <a class="nav-link <?= $isActive(['categorias']) ? 'active' : ''; ?>" href="<?= base_url('admin/categorias'); ?>">Categorías</a>
        <a class="nav-link <?= $isActive(['clientes']) ? 'active' : ''; ?>" href="<?= base_url('admin/clientes'); ?>">Clientes</a>

        <?php
        $infoClienteSlugs = [
            'info-cliente/editar/faq',
            'info-cliente/editar/envios',
            'info-cliente/editar/por_mayor',
            'info-cliente/editar/garantias',
            'info-cliente/editar/terminos',
            'info-cliente/editar/privacidad',
            'info-cliente/editar/cambios',
        ];
        $isInfoClienteOpen = $isActive($infoClienteSlugs);
        ?>

        <div class="nav-item mt-3 has-submenu <?= $isInfoClienteOpen ? 'open' : ''; ?>">
            <a href="#" class="nav-link submenu-toggle d-flex justify-content-between align-items-center">
                <span>Información para el cliente</span>
                <span class="arrow">▼</span>
            </a>

            <div class="nav flex-column ms-3 submenu">
                <a class="nav-link <?= $isActive(['info-cliente/editar/faq']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/faq'); ?>">Preguntas frecuentes</a>
                <a class="nav-link <?= $isActive(['info-cliente/editar/envios']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/envios'); ?>">Envíos a nivel nacional</a>
                <a class="nav-link <?= $isActive(['info-cliente/editar/por_mayor']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/por_mayor'); ?>">Pedidos por mayor</a>
                <a class="nav-link <?= $isActive(['info-cliente/editar/garantias']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/garantias'); ?>">Garantías</a>
                <a class="nav-link <?= $isActive(['info-cliente/editar/terminos']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/terminos'); ?>">Términos y condiciones</a>
                <a class="nav-link <?= $isActive(['info-cliente/editar/privacidad']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/privacidad'); ?>">Políticas de privacidad</a>
                <a class="nav-link <?= $isActive(['info-cliente/editar/cambios']) ? 'active' : ''; ?>" href="<?= base_url('admin/info-cliente/editar/cambios'); ?>">Cambios y devoluciones</a>
            </div>
        </div>

        <?php
        $sliderPaths = ['slider', 'slider/crear', 'slider/editar'];
        $isSliderOpen = $isActive($sliderPaths);
        ?>
        <div class="nav-item mt-3 has-submenu <?= $isSliderOpen ? 'open' : ''; ?>">
            <a href="#" class="nav-link submenu-toggle d-flex justify-content-between align-items-center">
                <span>Slider</span>
                <span class="arrow">▼</span>
            </a>

            <div class="nav flex-column ms-3 submenu">
                <a class="nav-link <?= $isActive(['slider']) ? 'active' : ''; ?>" href="<?= base_url('admin/slider'); ?>">Listar sliders</a>
                <a class="nav-link <?= $isActive(['slider/crear']) ? 'active' : ''; ?>" href="<?= base_url('admin/slider/crear'); ?>">Crear slider</a>
            </div>
        </div>

        <?php
        $experienciaPaths = ['experiencias', 'experiencias/crear', 'experiencias/editar'];
        $isExperienciasOpen = $isActive($experienciaPaths);
        ?>
        <div class="nav-item mt-3 has-submenu <?= $isExperienciasOpen ? 'open' : ''; ?>">
            <a href="#" class="nav-link submenu-toggle d-flex justify-content-between align-items-center">
                <span>Experiencias</span>
                <span class="arrow">▼</span>
            </a>

            <div class="nav flex-column ms-3 submenu">
                <a class="nav-link <?= $isActive(['experiencias']) ? 'active' : ''; ?>" href="<?= base_url('admin/experiencias'); ?>">Listar experiencias</a>
                <a class="nav-link <?= $isActive(['experiencias/crear']) ? 'active' : ''; ?>" href="<?= base_url('admin/experiencias/crear'); ?>">Crear nueva</a>
            </div>
        </div>

        <a class="nav-link <?= $isActive(['publicidad']) ? 'active' : ''; ?>" href="<?= base_url('admin/publicidad'); ?>">Publicidad</a>

        <a class="nav-link <?= $isActive(['informacion']) ? 'active' : ''; ?>" href="<?= base_url('admin/informacion'); ?>">Información</a>

        <a class="nav-link <?= $isActive(['usuarios']) ? 'active' : ''; ?>" href="<?= base_url('admin/usuarios'); ?>">Usuarios</a>

    </nav>

    <div class="mt-auto small text-white-50">
        <div>Sesión iniciada: <?= isset($currentUser['logged_in_at']) ? date('d/m/Y H:i', (int) $currentUser['logged_in_at']) : '-'; ?></div>
        <div><?= e($currentUser['rol'] ?? ''); ?></div>
    </div>
</aside>
<main class="admin-main flex-grow-1">
    <div class="admin-main__inner">
        <?php if (!empty($flash['message'])): ?>
            <div class="alert alert-<?= e($flash['type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <script>
            document.addEventListener("click", function(e) {
                const toggle = e.target.closest(".submenu-toggle");
                if (!toggle) return;
                e.preventDefault();
                const parent = toggle.closest(".has-submenu");
                if (parent) {
                    parent.classList.toggle("open");
                }
            });
        </script>