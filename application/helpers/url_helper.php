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
        return 'http://localhost/novedades/' . ltrim($path, '/');
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
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = rtrim("$scheme://$host/novedades", '/');

        $uploadsRel  = '/uploads/productos';
        $placeholder = $base . '/public/assets/img/no-image.jpg';

        if (!$rutaBD) {
            return $placeholder;
        }

        $rutaBD = trim($rutaBD, '/');
        $dir    = dirname($rutaBD);
        $file   = basename($rutaBD);
        $url    = $base . $uploadsRel . '/' . ($dir === '.' ? '' : $dir . '/') . rawurlencode($file);

        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $docRoot = rtrim($docRoot, '/\\');
        $local   = $docRoot . '/novedades' . $uploadsRel . '/' . ($dir === '.' ? '' : $dir . '/') . $file;

        return is_file($local) ? $url : $placeholder;
    }
}
