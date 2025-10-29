<?php

if (!function_exists('config_item')) {
    function config_item(string $key, $default = null)
    {
        global $config;
        return $config[$key] ?? $default;
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $base = rtrim(config_item('base_url', ''), '/');
        $path = ltrim($path, '/');
        return $path === '' ? $base . '/' : $base . '/' . $path;
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $path = ''): string
    {
        $assetBase = rtrim(config_item('asset_base', 'public/assets/'), '/');
        $path = ltrim($path, '/');
        return base_url($assetBase . ($path !== '' ? '/' . $path : ''));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }
}
