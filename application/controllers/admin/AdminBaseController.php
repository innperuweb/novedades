<?php

declare(strict_types=1);

abstract class AdminBaseController
{
    protected function render(string $view, array $data = []): void
    {
        $viewFile = ADMIN_VIEW_PATH . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(404);
            echo 'Vista de administración no encontrada.';
            return;
        }

        $currentUser = admin_current_user();
        $flash = admin_get_flash();
        $title = $data['title'] ?? 'Panel Administrativo';

        extract($data, EXTR_OVERWRITE);

        require ADMIN_VIEW_PATH . 'layouts/head.php';
        require ADMIN_VIEW_PATH . 'layouts/header.php';
        require ADMIN_VIEW_PATH . 'layouts/sidebar.php';
        require $viewFile;
        require ADMIN_VIEW_PATH . 'layouts/footer.php';
    }

    protected function renderAuth(string $view, array $data = []): void
    {
        $viewFile = ADMIN_VIEW_PATH . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(404);
            echo 'Vista de autenticación no encontrada.';
            return;
        }

        $flash = admin_get_flash();
        $title = $data['title'] ?? 'Iniciar sesión';

        extract($data, EXTR_OVERWRITE);

        require ADMIN_VIEW_PATH . 'layouts/auth_head.php';
        require $viewFile;
        require ADMIN_VIEW_PATH . 'layouts/auth_footer.php';
    }

    protected function requireLogin(): void
    {
        admin_require_login();
    }

    protected function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function validateCsrfToken(string $token): bool
    {
        if (!verify_csrf($token)) {
            admin_set_flash('danger', 'La sesión ha expirado. Por favor, inténtelo nuevamente.');

            return false;
        }

        return true;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url(trim($path, '/')));
        exit;
    }
}
