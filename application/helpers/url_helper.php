<?php

declare(strict_types=1);

if (!function_exists('config_item')) {
    function config_item(string $key, $default = null)
    {
        return $GLOBALS['config'][$key] ?? $default;
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $base = rtrim((string) config_item('base_url', ''), '/');
        $path = trim($path, '/');

        if ($base === '') {
            return $path === '' ? '/' : '/' . $path;
        }

        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

if (!function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        return base_url($path);
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $path = ''): string
    {
        $assetBase = trim((string) config_item('asset_base', 'assets'), '/');
        $path = ltrim($path, '/');

        $assetPath = $assetBase === '' ? $path : $assetBase . ($path !== '' ? '/' . $path : '');

        return base_url($assetPath);
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
