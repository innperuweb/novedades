<?php

declare(strict_types=1);

if (!function_exists('admin_session_key')) {
    function admin_session_key(): string
    {
        return 'ADMINSESS';
    }
}

if (!function_exists('admin_current_user')) {
    function admin_current_user(): ?array
    {
        return $_SESSION[admin_session_key()] ?? null;
    }
}

if (!function_exists('admin_is_logged_in')) {
    function admin_is_logged_in(): bool
    {
        $user = admin_current_user();

        return is_array($user) && isset($user['id']);
    }
}

if (!function_exists('admin_login_user')) {
    function admin_login_user(array $user): void
    {
        $_SESSION[admin_session_key()] = [
            'id' => (int) ($user['id'] ?? 0),
            'nombre' => (string) ($user['nombre'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'rol' => (string) ($user['rol'] ?? 'admin'),
            'activo' => (bool) ($user['activo'] ?? true),
            'logged_in_at' => time(),
        ];
    }
}

if (!function_exists('admin_logout')) {
    function admin_logout(): void
    {
        unset($_SESSION[admin_session_key()]);
    }
}

if (!function_exists('admin_require_login')) {
    function admin_require_login(): void
    {
        if (!admin_is_logged_in()) {
            admin_redirect_login();
        }
    }
}

if (!function_exists('admin_redirect_login')) {
    function admin_redirect_login(): void
    {
        header('Location: ' . base_url('admin/login'));
        exit;
    }
}

if (!function_exists('admin_set_flash')) {
    function admin_set_flash(string $type, string $message): void
    {
        $_SESSION['ADMIN_FLASH'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('admin_get_flash')) {
    function admin_get_flash(): ?array
    {
        $flash = $_SESSION['ADMIN_FLASH'] ?? null;
        if ($flash !== null) {
            unset($_SESSION['ADMIN_FLASH']);
        }

        return $flash;
    }
}

if (!function_exists('admin_available_roles')) {
    function admin_available_roles(): array
    {
        return [
            'admin' => 'Administrador',
            'editor' => 'Editor',
            'gestor' => 'Gestor',
        ];
    }
}
