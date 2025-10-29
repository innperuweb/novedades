<?php

if (!defined('ASSET_PATH')) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $baseURL = $scheme . '://' . $host;

    define('ASSET_PATH', rtrim($baseURL, '/') . '/application/vista/');
}
