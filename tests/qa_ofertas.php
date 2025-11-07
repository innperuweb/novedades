<?php

declare(strict_types=1);

set_error_handler(static function ($severity, $message, $file, $line): void {
    if (!(error_reporting() & $severity)) {
        return;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
putenv('DB_PERSISTENT=false');

require __DIR__ . '/../application/config/constants.php';
require __DIR__ . '/../application/helpers/security_helper.php';
require __DIR__ . '/../application/helpers/url_helper.php';
require __DIR__ . '/../database.php';
require __DIR__ . '/../application/models/ProductoModel.php';

$pdo = Database::connect();

$schema = [
    'CREATE TABLE productos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL,
        precio REAL NOT NULL,
        categoria_slug TEXT,
        visible INTEGER NOT NULL,
        estado INTEGER NOT NULL,
        stock INTEGER NOT NULL
    )',
    'CREATE TABLE producto_imagenes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        producto_id INTEGER NOT NULL,
        nombre TEXT NOT NULL,
        ruta TEXT,
        es_principal INTEGER DEFAULT 0,
        orden INTEGER DEFAULT 0
    )',
    'CREATE TABLE producto_categorias_web (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        producto_id INTEGER NOT NULL,
        seccion TEXT NOT NULL
    )',
];

foreach ($schema as $sql) {
    $pdo->exec($sql);
}

$pdo->exec("INSERT INTO productos (id, nombre, precio, categoria_slug, visible, estado, stock) VALUES
    (101, 'Producto Oferta Especial', 129.9, 'muebles', 1, 1, 25),
    (102, 'Producto Oferta Directa', 89.5, 'ofertas', 1, 1, 15),
    (103, 'Producto Oculto', 49.9, 'ofertas', 0, 1, 5)
");

$pdo->exec("INSERT INTO producto_imagenes (producto_id, nombre, ruta, es_principal, orden) VALUES
    (101, '1_oferta.webp', 'uploads/productos/101/1_oferta.webp', 1, 1),
    (102, '1_directa.webp', 'uploads/productos/102/1_directa.webp', 1, 1)
");

$pdo->exec("INSERT INTO producto_categorias_web (producto_id, seccion) VALUES
    (101, 'ofertas'),
    (102, 'ofertas'),
    (103, 'ofertas')
");

$uploadsBase = __DIR__ . '/../public/assets/uploads/productos';
@mkdir($uploadsBase, 0777, true);

$imagenes = [
    101 => '1_oferta.webp',
    102 => '1_directa.webp',
];

foreach ($imagenes as $productoId => $archivo) {
    $dir = $uploadsBase . '/' . $productoId;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $ruta = $dir . '/' . $archivo;
    if (!is_file($ruta)) {
        file_put_contents($ruta, 'test');
    }
}

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/..');

$model = new ProductoModel();
$ofertas = $model->obtenerProductosPorSeccion('ofertas', 10);

if (count($ofertas) < 2) {
    throw new RuntimeException('Se esperaban al menos dos productos visibles en ofertas.');
}

$primerProducto = $ofertas[0];
if (empty($primerProducto['ruta_principal'])) {
    throw new RuntimeException('El primer producto no contiene ruta_principal.');
}

$productosAleatorios = [];
$novedades = [];
$populares = [];

ob_start();
include VIEW_PATH . 'index.php';
$html = ob_get_clean();

if (strpos($html, 'Producto Oferta Especial') === false) {
    throw new RuntimeException('No se encontró el nombre del producto en el HTML renderizado.');
}

if (strpos($html, 'Producto Oferta Directa') === false) {
    throw new RuntimeException('No se encontró el segundo producto en el HTML renderizado.');
}

if (strpos($html, 'public/assets/uploads/productos/101/1_oferta.webp') === false) {
    throw new RuntimeException('No se encontró la imagen esperada del producto.');
}

echo "QA ofertas completado con éxito.\n";
