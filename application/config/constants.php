<?php
if (!defined('ASSET_PATH')) {
    $publicRoot = '/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $projectRoot = realpath(dirname(__DIR__, 2));

    if ($documentRoot !== '' && $projectRoot !== false) {
        $documentRootReal = realpath($documentRoot);

        if ($documentRootReal !== false) {
            $documentRootNormalized = rtrim(str_replace('\\', '/', $documentRootReal), '/');
            $projectRootNormalized = rtrim(str_replace('\\', '/', $projectRoot), '/');

            if ($documentRootNormalized !== '' && strpos($projectRootNormalized, $documentRootNormalized) === 0) {
                $relativePath = substr($projectRootNormalized, strlen($documentRootNormalized));
                $relativePath = '/' . ltrim($relativePath, '/');
                $publicRoot = $relativePath === '/' ? '/' : rtrim($relativePath, '/') . '/';
            }
        }
    }

    if ($publicRoot === '') {
        $publicRoot = '/';
    }

    define('ASSET_PATH', $publicRoot);
}
