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
        // Detectar esquema (http/https)
        $isHttps        = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;

        if (!empty($forwardedProto)) {
            $scheme = explode(',', (string) $forwardedProto)[0];
        } else {
            $scheme = $isHttps ? 'https' : 'http';
        }

        // Host
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

        // Path actual del script
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $directory  = str_replace('\\', '/', dirname($scriptName));

        // 👇 Ajuste clave
        // Si la ruta termina en "/admin" o contiene "/admin/", subimos un nivel
        if (preg_match('~/admin$~', $directory)) {
            $directory = dirname($directory);
        }

        // Normalizar directory
        if ($directory === '/' || $directory === '.' || $directory === '\\') {
            $directory = '';
        } else {
            $directory = rtrim($directory, '/');
        }

        // Base final
        $basePath = $directory !== '' ? $directory . '/' : '/';
        $base     = sprintf('%s://%s%s', $scheme, $host, $basePath);

        // Agregar path
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

if (!function_exists('imagen_principal_producto')) {
    /**
     * Retorna la URL de la imagen principal de un producto, usando ruta_principal del modelo.
     */
    function imagen_principal_producto(array $producto): string
    {
        $id = (int)($producto['id'] ?? 0);
        $rutaBD = $producto['ruta_principal'] ?? null;

        // Usa el helper central url_imagen_producto()
        return url_imagen_producto($id, $rutaBD);
    }
}


if (!function_exists('url_imagen_producto')) {
    function url_imagen_producto(int $productoId, ?string $rutaBD): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base = rtrim(base_url(), '/');

        $uploadsRel  = '/public/assets/uploads/productos';
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
        $uploadsPrefix = 'uploads/productos';
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

        $docRoot   = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
        $basePath  = parse_url(base_url(), PHP_URL_PATH) ?? '';
        $basePath  = rtrim($basePath, '/');
        $localPath = $docRoot . ($basePath !== '' ? $basePath : '') . $urlPath . $file;

        return is_file($localPath) ? $url : $placeholder;
    }
}
