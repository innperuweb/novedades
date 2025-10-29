<?php

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$basePath = $dir !== '' ? $dir : '';
$baseUrl = $scheme . $host . $basePath . '/';

return [
    'base_url' => $baseUrl,
    'asset_base' => 'public/assets/',
    'default_controller' => 'HomeController',
    'default_method' => 'index',
];
