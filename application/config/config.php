<?php

declare(strict_types=1);

$environment = getenv('APP_ENV') ?: (defined('ENVIRONMENT') ? ENVIRONMENT : 'production');
$environment = strtolower((string) $environment);

if (!in_array($environment, ['production', 'staging', 'development', 'testing'], true)) {
    $environment = 'production';
}

/**
 * Resolve the base URL taking into account CLI usage, proxies and sub-directory deployments.
 */
$baseUrl = static function (): string {
    $configuredUrl = getenv('APP_URL');

    if ($configuredUrl) {
        return rtrim($configuredUrl, '/') . '/';
    }

    if (PHP_SAPI === 'cli') {
        return 'http://localhost/';
    }

    $https = $_SERVER['HTTPS'] ?? null;
    $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;
    $scheme = 'http';

    if (!empty($forwardedProto)) {
        $scheme = explode(',', (string) $forwardedProto)[0];
    } elseif (!empty($https) && $https !== 'off') {
        $scheme = 'https';
    }

    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $directory = str_replace('\\', '/', dirname($scriptName));
    $directory = ($directory === '/' || $directory === '.') ? '' : trim($directory, '/');
    $basePath = $directory !== '' ? '/' . $directory : '';

    return sprintf('%s://%s%s/', $scheme, $host, $basePath);
};

return [
    'environment' => $environment,
    'base_url' => $baseUrl(),
    'asset_base' => 'assets',
    'default_controller' => 'HomeController',
    'default_method' => 'index',
];
