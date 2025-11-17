<?php

declare(strict_types=1);

require_once __DIR__ . '/../application/helpers/url_helper.php';

// Preparar entorno simulado
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTPS'] = '';

$tempRoot = sys_get_temp_dir() . '/imagen_principal_test';
if (is_dir($tempRoot)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $file) {
        $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
    }
    rmdir($tempRoot);
}

$_SERVER['DOCUMENT_ROOT'] = $tempRoot;

$imagenesSimuladas = [
    ['producto_id' => 18, 'ruta' => 'uploads/productos/18/1_690cff061230c6.10920117.webp'],
    ['producto_id' => 19, 'ruta' => 'uploads/productos/19/2_demo_imagen.webp'],
    ['producto_id' => 20, 'ruta' => 'uploads/productos/20/3_demo_imagen.webp'],
];

$placeholder = base_url('public/assets/img/no-image.jpg');
$urlsGeneradas = [];

foreach ($imagenesSimuladas as $imagen) {
    $ruta = (string) $imagen['ruta'];
    $productoId = (int) $imagen['producto_id'];

    $rutaLocal = $tempRoot . '/public/assets/' . $ruta;
    $directorio = dirname($rutaLocal);
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }
    file_put_contents($rutaLocal, 'prueba');

    $urlsGeneradas[] = url_imagen_producto($productoId, $ruta);
}

if (count(array_unique($urlsGeneradas)) !== count($urlsGeneradas)) {
    throw new RuntimeException('Las URLs generadas deben ser únicas por producto simulado.');
}

$sinRuta = url_imagen_producto(21, null);
if ($sinRuta !== $placeholder) {
    throw new RuntimeException('La imagen faltante debe usar el placeholder configurado.');
}

echo "Prueba de imágenes principales completada con éxito.\n";
foreach ($urlsGeneradas as $url) {
    echo $url . "\n";
}
echo $sinRuta . "\n";

// Limpieza
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($tempRoot, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($iterator as $file) {
    $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
}
rmdir($tempRoot);
