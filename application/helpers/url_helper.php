<?php

declare(strict_types=1);

if (!function_exists('config_item')) {
    function config_item(string $key, $default = null)
    {
        return $GLOBALS['config'][$key] ?? $default;
    }
}

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;
        if (!empty($forwardedProto)) {
            $scheme = explode(',', (string) $forwardedProto)[0];
        } else {
            $scheme = $isHttps ? 'https' : 'http';
        }
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $directory = str_replace('\\', '/', dirname($scriptName));
        $directory = ($directory === '/' || $directory === '.') ? '' : rtrim($directory, '/');
        $basePath = $directory !== '' ? $directory . '/' : '/';
        $base = sprintf('%s://%s%s', $scheme, $host, $basePath);
        $normalizedPath = ltrim((string) $path, '/');
        return $base . $normalizedPath;
    }
}

if (!function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        return base_url($path);
    }
}

if (!function_exists('asset_url')) {
    function asset_url($path = '')
    {
        return base_url('public/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $statusCode = 302): void
    {
        header('Location: ' . site_url($path), true, $statusCode);
        exit;
    }
}

if (!function_exists('current_url')) {
    function current_url(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . $host . $requestUri;
    }
}

if (!function_exists('url_imagen_producto')) {
    function url_imagen_producto(int $productoId, ?string $rutaBD): string
    {
        $base = rtrim(base_url(), '/');
        $placeholder = $base . '/public/assets/img/no-image.jpg';

        if ($rutaBD === null) {
            return $placeholder;
        }

        $rutaBD = str_replace('\\', '/', (string) $rutaBD);
        $rutaBD = trim($rutaBD);

        if ($rutaBD === '') {
            return $placeholder;
        }

        if (preg_match('~^https?://~i', $rutaBD)) {
            return $rutaBD;
        }

        $rutaBD = ltrim($rutaBD, '/');

        if (stripos($rutaBD, 'public/assets/uploads/productos/') === 0) {
            return base_url($rutaBD);
        }

        if (stripos($rutaBD, 'uploads/productos/') === 0 || stripos($rutaBD, 'uploads/productos') === 0) {
            return base_url('public/assets/' . $rutaBD);
        }

        return base_url($rutaBD);
    }
}
