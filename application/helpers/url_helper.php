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

$rutaBD = str_replace('\\', '/', (string) $rutaBD);
$rutaBD = trim($rutaBD, '/');

// Si está vacío, usa imagen por defecto
if ($rutaBD === '') {
    return $placeholder;
}

// Si ya es una URL absoluta, devuélvela tal cual
if (preg_match('~^https?://~i', $rutaBD)) {
    return $rutaBD;
}

// Elimina el prefijo "uploads/productos/" si ya viene incluido
$uploadsPrefix = ltrim($uploadsRel, '/');
if (stripos($rutaBD, $uploadsPrefix . '/') === 0) {
    $rutaBD = substr($rutaBD, strlen($uploadsPrefix) + 1);
}

// Separa directorio y archivo
$dir  = dirname($rutaBD);
$file = basename($rutaBD);

// Validar nombre del archivo
if ($file === '' || $file === '.' || $file === '..') {
    return $placeholder;
}

// Construir URL y ruta local
$relativeDir = $dir === '.' ? '' : trim($dir, '/');
$urlPath = $uploadsRel . '/' . ($relativeDir === '' ? '' : $relativeDir . '/');
$url     = $base . $urlPath . rawurlencode($file);

$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
$local   = $docRoot . '/novedades' . $urlPath . $file;

return is_file($local) ? $url : $placeholder;


        return is_file($local) ? $url : $placeholder;
    }
}
