<?php
require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/ProductoModel.php';

$productoModel = new ProductoModel();
$producto = $producto ?? null;
$id = isset($_GET['id']) ? sanitize_int($_GET['id']) : null;

if ($producto === null) {
    if ($id === null) {
        echo '<div class="container"><p>Producto no encontrado.</p></div>';
        return;
    }

    $producto = $productoModel->getById($id);
}

if ($producto === null) {
    echo '<div class="container"><p>Producto no disponible.</p></div>';
    return;
}

$productoId = (int) ($producto['id'] ?? 0);
$productoNombre = e($producto['nombre'] ?? 'Producto');
$productoPrecio = (float) ($producto['precio'] ?? 0);
$normalizarOpciones = static function ($valor): array {
    if (!is_array($valor)) {
        if ($valor === null || $valor === '') {
            return [];
        }

        $valorCadena = (string) $valor;
        $decoded = json_decode($valorCadena, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $valor = $decoded;
        } else {
            $valor = preg_split('/[;,]+/', $valorCadena) ?: [];
        }
    }

    $items = array_map(static fn($item): string => trim((string) $item), $valor);
    $items = array_filter($items, static fn($item): bool => $item !== '');

    return array_values(array_unique($items));
};

$coloresDisponibles = $normalizarOpciones($producto['colores'] ?? []);
$tallasDisponibles = $normalizarOpciones($producto['tallas'] ?? []);
$productosRelacionados = $productosRelacionados ?? [];

if (empty($productosRelacionados)) {
    try {
        $todosLosProductos = $productoModel->getAll();
    } catch (\Throwable $exception) {
        $todosLosProductos = [];
    }

    if (!empty($todosLosProductos)) {
        $productosRelacionados = array_values(array_filter($todosLosProductos, function ($item) use ($productoId) {
            return (int) ($item['id'] ?? 0) !== $productoId;
        }));
    }
}

if (!empty($productosRelacionados)) {
    $productosRelacionados = array_slice($productosRelacionados, 0, 10);
}

$imagenes = $imagenes ?? null;
if ($imagenes === null) {
    $imagenes = $producto['imagenes'] ?? [];
}

if (!is_array($imagenes)) {
    $imagenes = [];
}

$galeriaImagenes = [];
foreach ($imagenes as $indice => $itemImagen) {
    if (is_array($itemImagen)) {
        $ruta = trim((string) ($itemImagen['ruta'] ?? ''));
        $principal = (int) ($itemImagen['es_principal'] ?? 0);
        $orden = (int) ($itemImagen['orden'] ?? $indice);
    } else {
        $ruta = trim((string) $itemImagen);
        $principal = 0;
        $orden = $indice;
    }

    if ($ruta === '') {
        continue;
    }

    $galeriaImagenes[] = [
        'ruta' => $ruta,
        'es_principal' => $principal,
        'orden' => $orden,
    ];
}

if ($galeriaImagenes === []) {
    $galeriaImagenes[] = [
        'ruta' => 'default:no-image.jpg',
        'es_principal' => 1,
        'orden' => 0,
    ];
}

usort($galeriaImagenes, static function ($a, $b): int {
    $principalA = (int) ($a['es_principal'] ?? 0);
    $principalB = (int) ($b['es_principal'] ?? 0);

    if ($principalA !== $principalB) {
        return $principalB <=> $principalA;
    }

    $ordenA = (int) ($a['orden'] ?? 0);
    $ordenB = (int) ($b['orden'] ?? 0);

    return $ordenA <=> $ordenB;
});

$principalAsignado = false;
foreach ($galeriaImagenes as &$imagenOrdenada) {
    if ((int) ($imagenOrdenada['es_principal'] ?? 0) === 1 && !$principalAsignado) {
        $principalAsignado = true;
    }
}
unset($imagenOrdenada);

if (!$principalAsignado && $galeriaImagenes !== []) {
    $galeriaImagenes[0]['es_principal'] = 1;
}

$normalizarRuta = static function (string $ruta): string {
    $ruta = trim($ruta);

    if ($ruta === '') {
        return '';
    }

    if (strpos($ruta, 'default:') === 0) {
        $archivo = substr($ruta, strlen('default:')) ?: '';
        $archivo = ltrim($archivo, '/');

        return asset_url('img/' . $archivo);
    }

    if (strpos($ruta, 'legacy:') === 0) {
        $ruta = substr($ruta, strlen('legacy:')) ?: '';
        $ruta = ltrim($ruta, '/');

        return asset_url('img/products/' . $ruta);
    }

    if (preg_match('#^https?://#i', $ruta) === 1) {
        return $ruta;
    }

    $rutaLimpia = ltrim($ruta, '/');

    if (strpos($rutaLimpia, 'public/assets/') === 0) {
        $rutaLimpia = ltrim(substr($rutaLimpia, strlen('public/assets/')) ?: '', '/');
    }

    if (strpos($rutaLimpia, 'assets/') === 0) {
        $rutaLimpia = ltrim(substr($rutaLimpia, strlen('assets/')) ?: '', '/');
    }

    if (strpos($rutaLimpia, 'public/uploads/productos/') === 0) {
        return base_url($rutaLimpia);
    }

    if (strpos($rutaLimpia, 'uploads/') === 0) {
        return asset_url($rutaLimpia);
    }

    if (strpos($rutaLimpia, 'products/') === 0 || strpos($rutaLimpia, 'productos/') === 0) {
        return asset_url('uploads/' . $rutaLimpia);
    }

    if (strpos($rutaLimpia, 'tabla_tallas/') === 0) {
        return asset_url('uploads/' . $rutaLimpia);
    }

    if (strpos($rutaLimpia, 'uploads/productos/') === 0) {
        $rutaLimpia = substr($rutaLimpia, strlen('uploads/productos/')) ?: '';
    }

    return asset_url('uploads/productos/' . $rutaLimpia);
};

foreach ($galeriaImagenes as &$imagenNormalizada) {
    $imagenNormalizada['url'] = $normalizarRuta((string) ($imagenNormalizada['ruta'] ?? ''));
}
unset($imagenNormalizada);

$galeriaImagenes = array_values(array_filter($galeriaImagenes, static fn($item): bool => ($item['url'] ?? '') !== ''));

if ($galeriaImagenes === []) {
    $galeriaImagenes[] = [
        'ruta' => 'default:no-image.jpg',
        'es_principal' => 1,
        'orden' => 0,
        'url' => asset_url('img/no-image.jpg'),
    ];
}

$sliderMainId = 'product-gallery-main-' . $productoId;
$sliderThumbId = 'product-gallery-thumb-' . $productoId;
$slidesThumbs = max(1, min(4, count($galeriaImagenes)));

$thumbOptions = json_encode([
    'vertical' => true,
    'verticalSwiping' => true,
    'slidesToShow' => $slidesThumbs,
    'asNavFor' => '#' . $sliderMainId,
    'focusOnSelect' => true,
    'arrows' => true,
    'infinite' => false,
], JSON_UNESCAPED_SLASHES);

$thumbResponsive = json_encode([
    ['breakpoint' => 1200, 'settings' => ['vertical' => true, 'verticalSwiping' => true, 'slidesToShow' => min($slidesThumbs, 4)]],
    ['breakpoint' => 992, 'settings' => ['vertical' => false, 'verticalSwiping' => false, 'slidesToShow' => min($slidesThumbs, 4)]],
    ['breakpoint' => 768, 'settings' => ['vertical' => false, 'verticalSwiping' => false, 'slidesToShow' => min($slidesThumbs, 3)]],
    ['breakpoint' => 576, 'settings' => ['vertical' => false, 'verticalSwiping' => false, 'slidesToShow' => min($slidesThumbs, 2)]],
], JSON_UNESCAPED_SLASHES);

$mainOptions = json_encode([
    'slidesToShow' => 1,
    'arrows' => false,
    'asNavFor' => '#' . $sliderThumbId,
    'infinite' => false,
    'adaptiveHeight' => true,
], JSON_UNESCAPED_SLASHES);

$stockCantidad = (int) ($producto['stock'] ?? 0);
$tablaTallasArchivo = trim((string) ($producto['tabla_tallas'] ?? ''));
$tablaTallasUrl = $tablaTallasArchivo !== '' ? $normalizarRuta($tablaTallasArchivo) : '';
?>

<style>
    .input-error {
        border: 2px solid #ff4d4d !important;
        background-color: #fff0f0;
    }

    /* Estilo para los mensajes de error */
    .error-message {
        color: #ff4d4d;
        font-size: 0.9em;
        margin-top: 5px;
        display: none;
    }

    /* Mejora la visibilidad de los selectores */
    .form__input--2 {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
        margin-bottom: 10px;
    }

    /* Estilo para el formulario */
    .form-add-cart {
        margin-bottom: 20px;
    }

    .product-card a {
        text-decoration: none;
        color: inherit;
    }

    .product-card img {
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .product-card img:hover {
        transform: scale(1.05);
    }

    .stock.disponible {
        color: #2ecc71;
        font-weight: bold;
    }

    .stock.agotado {
        color: #e74c3c;
        font-weight: bold;
    }

    .product-size-chart img,
    .tabla-tallas img {
        max-width: 100%;
        height: auto;
        display: block;
    }
</style>

<div class="breadcrumb-area pt--70 pt-md--25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <ul class="breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop-sidebar.html">Shop Pages</a></li>
                    <li class="current"><span>Product Configurable</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper">
    <div class="page-content-inner enable-full-width">
        <div class="container-fluid">
            <div class="row pt--40">
                <div class="col-md-6 product-main-image">
                    <div class="product-image">
                        <div class="product-gallery vertical-slide-nav">
                            <div class="product-gallery__thumb">
                                <div id="<?= e($sliderThumbId); ?>"
                                    class="airi-element-carousel nav-slider"
                                    data-slick-options='<?= e($thumbOptions); ?>'
                                    data-slick-responsive='<?= e($thumbResponsive); ?>'>
                                    <?php foreach ($galeriaImagenes as $img): ?>
                                        <div class="product-gallery__thumb--single">
                                            <img src="<?= e($img['url']); ?>" alt="<?= e($productoNombre); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="product-gallery__large-image">
                                <div id="<?= e($sliderMainId); ?>"
                                    class="airi-element-carousel product-gallery__image image-slider"
                                    data-slick-options='<?= e($mainOptions); ?>'>
                                    <?php foreach ($galeriaImagenes as $img): ?>
                                        <div class="product-gallery__image--single product-gallery__item">
                                            <img src="<?= e($img['url']); ?>" alt="<?= e($productoNombre); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <span class="product-badge new">New</span>
                    </div>
                </div>
                <div class="col-md-6 product-main-details mt-md--10 mt-sm--30">
                    <div class="product-summary">
                        <div class="clearfix"></div>
                        <h3 class="product-title"><?= $productoNombre ?></h3>

                        <?php if (!empty($producto['marca'])): ?>
                            <p><strong>Marca:</strong> <?= e($producto['marca']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($producto['sku'])): ?>
                            <p><strong>SKU:</strong> <?= e($producto['sku']) ?></p>
                        <?php endif; ?>

                        <div class="stock-row">
                            <?php if ($stockCantidad > 0): ?>
                                <span class="stock disponible">Con Stock</span>
                            <?php else: ?>
                                <span class="stock agotado">Sin Stock</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-price-wrapper mb--40 mb-md--10">
                            <span class="money">S/ <?= number_format($productoPrecio, 2) ?></span>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (!empty($producto['descripcion'])): ?>
                            <p class="product-short-description mb--45 mb-sm--20">
                                <?= nl2br(e($producto['descripcion'])) ?>
                            </p>
                        <?php else: ?>
                            <p class="product-short-description mb--45 mb-sm--20 text-muted">
                                Sin descripción disponible.
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($producto['tabla_tallas'])): ?>
                            <div class="tabla-tallas-container mt--20">
                                <p style="text-align:left; margin-top:20px;">
                                    <strong>
                                        <a href="#" id="abrirTablaTallas" class="tabla-tallas-link"> <i class="fa-solid fa-ruler"></i> TABLA DE TALLAS</a>
                                    </strong>
                                </p>
                            </div>

                            <!-- Modal (Popup) -->
                            <div id="tablaTallasModal" class="tabla-tallas-modal">
                                <div class="tabla-tallas-modal-content">
                                    <span class="tabla-tallas-cerrar">&times;</span>
                                    <img src="<?= asset_url($producto['tabla_tallas']) ?>" alt="Tabla de tallas" class="tabla-tallas-img">
                                </div>
                            </div>

                            <style>
                                /* ===== POPUP TABLA DE TALLAS ===== */
                                .tabla-tallas-link {
                                    color: #00aeed;
                                    text-decoration: none;
                                    font-size: 14px;
                                    transition: color 0.3s;
                                }

                                .tabla-tallas-link:hover {
                                    color: #c0392b;
                                }

                                .tabla-tallas-modal {
                                    display: none;
                                    position: fixed;
                                    z-index: 9999;
                                    left: 0;
                                    top: 0;
                                    width: 100%;
                                    height: 100%;
                                    overflow: hidden;
                                    background-color: rgba(0, 0, 0, 0.7);
                                }

                                .tabla-tallas-modal-content {
                                    position: relative;
                                    margin: 20px auto;
                                    padding: 20px;
                                    width: 95%;
                                    max-width: 800px;
                                    background: #fff;
                                    border-radius: 8px;
                                    text-align: center;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
                                }

                                .tabla-tallas-img {
                                    max-width: 100%;
                                    height: auto;
                                    border-radius: 6px;
                                    display: block;
                                    margin: 20px auto;
                                }

                                .tabla-tallas-cerrar {
                                    position: absolute;
                                    top: 10px;
                                    right: 15px;
                                    color: #333;
                                    font-size: 28px;
                                    font-weight: bold;
                                    cursor: pointer;
                                }

                                .tabla-tallas-cerrar:hover {
                                    color: #c0392b;
                                }

                                @media (max-width: 768px) {
                                    .tabla-tallas-modal-content {
                                        width: 90%;
                                        max-width: 90%;
                                        padding: 10px;
                                    }

                                    .tabla-tallas-img {
                                        margin-top: 10px;
                                    }
                                }
                            </style>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const modal = document.getElementById('tablaTallasModal');
                                    const abrir = document.getElementById('abrirTablaTallas');
                                    const cerrar = document.querySelector('.tabla-tallas-cerrar');

                                    abrir.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        modal.style.display = 'block';
                                    });

                                    cerrar.addEventListener('click', function() {
                                        modal.style.display = 'none';
                                    });

                                    window.addEventListener('click', function(e) {
                                        if (e.target === modal) modal.style.display = 'none';
                                    });
                                });
                            </script>
                        <?php endif; ?>

                        <br>

                        <form method="POST" action="<?= base_url('carrito/agregar') ?>" class="variation-form mb--35 form-add-cart">
                            <input type="hidden" name="id" value="<?= e((string) $productoId) ?>">

                            <?php if ($coloresDisponibles !== []): ?>
                                <div class="product-color-variations mb--20">
                                    <label for="color" class="swatch-label">Color:</label>
                                    <select name="color" id="color" class="form__input form__input--2" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($coloresDisponibles as $color): ?>
                                            <option value="<?= e($color) ?>"><?= e($color) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <?php if ($tallasDisponibles !== []): ?>
                                <div class="product-size-variations">
                                    <label for="talla" class="swatch-label">Talla:</label>
                                    <select name="talla" id="talla" class="form__input form__input--2" required>
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($tallasDisponibles as $talla): ?>
                                            <option value="<?= e($talla) ?>"><?= e($talla) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="form--action mb--30 mb-sm--20">
                                <div class="product-action flex-row align-items-center">
                                    <div class="quantity">
                                        <input type="number" class="quantity-input" name="cantidad" id="qty" value="1" min="1">
                                    </div>
                                    <button type="submit" class="btn btn-style-1 btn-large">
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </form>
                        <article class="single-post-details recojo_envio">
                            <div class="entry-footer-meta">
                                <div class="tag-list">
                                    <span>
                                        <i class="fa-solid fa-store icono_celeste"></i>
                                    </span>
                                    <span>
                                        &nbsp;&nbsp;Recojo en zonas específicas (Consultar Zonas):
                                    </span>
                                </div>
                                <div class="author">
                                    <span>
                                        Sin recargo
                                    </span>
                                </div>
                            </div>
                            <div class="entry-footer-meta">
                                <div class="tag-list">
                                    <span>
                                        <i class="fa-solid fa-motorcycle icono_celeste"></i>
                                    </span>
                                    <span>
                                        &nbsp;&nbsp;Delivery en Lima (Consultar cobertura):
                                    </span>
                                </div>
                                <div class="author">
                                    <span>
                                        S/ 10.00
                                    </span>
                                </div>
                            </div>
                            <div class="entry-footer-meta">
                                <div class="tag-list">
                                    <span>
                                        <i class="fa-solid fa-truck icono_celeste"></i>
                                    </span>
                                    <span>
                                        &nbsp;&nbsp;Envío por encomienda Provincia/Lima (Terrestre)
                                    </span>
                                </div>
                                <div class="author">
                                    <span>
                                        S/ 12.00
                                    </span>
                                </div>
                            </div>
                            <div class="entry-footer-meta">
                                <div class="tag-list">
                                    <span>
                                        <i class="fa-solid fa-plane icono_celeste"></i>
                                    </span>
                                    <span>
                                        &nbsp;&nbsp;Envío por encomienda Provincia (Aéreo)
                                    </span>
                                </div>
                                <div class="author">
                                    <span>
                                        S/ 18.00
                                    </span>
                                </div>
                            </div>
                            <div class="entry-footer-meta">
                                <div class="tag-list">
                                    <span>
                                        <b> • Tarifas pueden varias según la cobertura, tamaño y peso de bulto. </b>
                                    </span>
                                </div>
                            </div>

                        </article><br>
                        <span class="sku_wrapper font-size-12">MÉTODOS DE PAGO:</span> <br>
                        <img src="<?= asset_url('img/others/payments.png'); ?>" alt="Payment">
                    </div>
                </div>
            </div>

            <!--------
            <div class="row justify-content-center pt--45 pt-lg--50 pt-md--55 pt-sm--35">
                <div class="col-12">
                    <div class="product-data-tab tab-style-1">
                        <div class="nav nav-tabs product-data-tab__head mb--40 mb-md--30" id="product-tab" role="tablist">
                            <button type="button" class="product-data-tab__link nav-link active" id="nav-description-tab" data-bs-toggle="tab" data-bs-target="#nav-description" role="tab" aria-selected="true">
                                <span>Descripción</span>
                            </button>
                            <button type="button" class="product-data-tab__link nav-link" id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews" role="tab" aria-selected="true">
                                <span>Comentarios</span>
                            </button>
                        </div>
                        <div class="tab-content product-data-tab__content" id="product-tabContent">
                            <div class="tab-pane fade show active" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab">
                                <div class="product-description">
                                    <p>Donec accumsan auctor iaculis. Sed suscipit arcu ligula, at egestas magna molestie a. Proin ac ex maximus, ultrices justo eget, sodales orci. Aliquam egestas libero ac turpis pharetra, in vehicula lacus scelerisque.
                                        Vestibulum ut sem laoreet, feugiat tellus at, hendrerit arcu.

                                    <p>Nunc lacus elit, faucibus ac laoreet sed, dapibus ac mi. Maecenas eu ante a elit tempus fermentum. Aliquam commodo tincidunt semper. Phasellus accumsan, justo ac mollis pharetra, ex dui pharetra nisl, a
                                        scelerisque ipsum nulla ac sem. Cras eu risus urna. Duis lorem sapien, congue eget nisl sit amet, rutrum faucibus elit.</p>

                                    <ul>
                                        <li>Maecenas eu ante a elit tempus fermentum. Aliquam commodo tincidunt semper</li>
                                        <li>Aliquam est et tempus. Eaecenas libero ante, tincidunt vel</li>
                                    </ul>

                                    <p>Curabitur sodales euismod nibh. Sed iaculis sed orci eget semper. Nam auctor, augue et eleifend tincidunt, felis mauris convallis neque, in placerat metus urna laoreet diam. Morbi sagittis facilisis arcu
                                        sed ornare. Maecenas dictum urna ut facilisis rhoncus.iaculis sed orci eget semper. Nam auctor, augue et eleifend tincidunt, felis mauris</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-reviews" role="tabpanel" aria-labelledby="nav-reviews-tab">

                                <div class="product-reviews">
                                    <ul class="review__list">
                                        <li class="review__item">
                                            <div class="review__container">
                                                <img src="<?= asset_url('img/others/comment-icon-2.jpg'); ?>" alt="Review Avatar" class="review__avatar">
                                                <div class="review__text">
                                                    <div class="review__meta">
                                                        <strong class="review__author">Jared Conde</strong>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <p class="review__description">Aliquam egestas libero ac turpis pharetra, in vehicula lacus scelerisque. Vestibulum ut sem laoreet, feugiat tellus at, hendrerit arcu.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <h3 class="review__title"></h3>
                                </div>

                                <div class="review-form-wrapper">
                                    <form action="#" class="form">
                                        <div class="form__group mb--30 mb-sm--20">
                                            <div class="row">
                                                <div class="col-sm-6 mb-sm--20">
                                                    <label class="form__label" for="name">Nombre<span class="required">*</span></label>
                                                    <input type="text" name="name" id="name" class="form__input">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form__label" for="email">Email<span class="required">*</span></label>
                                                    <input type="email" name="email" id="email" class="form__input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form__group mb--30 mb-sm--20">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label class="form__label" for="email">Comentario<span class="required">*</span></label>
                                                    <textarea name="review" id="review" class="form__input form__input--textarea"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form__group">
                                            <div class="row">
                                                <div class="col-12">
                                                    <input type="submit" value="Enviar" class="btn btn-style-1 btn-submit">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            -------->

            <div class="row pt--35 pt-md--25 pt-sm--15 pb--75 pb-md--55 pb-sm--35">
                <div class="col-12">
                    <div class="row mb--40 mb-md--30">
                        <div class="col-12 text-center">
                            <h2 class="heading-secondary">Productos relacionados</h2>
                            <hr class="separator center mt--25 mt-md--15">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="airi-element-carousel product-carousel nav-vertical-center" data-slick-options='{
                                    "spaceBetween": 30,
                                    "slidesToShow": 5,
                                    "slidesToScroll": 1,
                                    "arrows": true,
                                    "prevArrow": "dl-icon-left",
                                    "nextArrow": "dl-icon-right"
                                    }' data-slick-responsive='[
                                        {"breakpoint":1200, "settings": {"slidesToShow": 3} },
                                        {"breakpoint":991, "settings": {"slidesToShow": 2} },
                                        {"breakpoint":450, "settings": {"slidesToShow": 1} }
                                    ]'>
                                <?php if (empty($productosRelacionados)): ?>
                                    <p class="text-center w-100">No hay productos relacionados disponibles.</p>
                                <?php else: ?>
                                    <?php foreach ($productosRelacionados as $rel): ?>
                                        <?php $relId = (string) ($rel['id'] ?? ''); ?>
                                        <div class="product-card">
                                            <div class="product-img">
                                                <a href="<?= base_url('detalle_producto.php?id=' . urlencode($relId)) ?>">
                                                    <img src="<?= asset_url('img/products/' . e($rel['imagen'] ?? 'producto1.jpg')) ?>"
                                                        alt="<?= e($rel['nombre'] ?? 'Producto relacionado') ?>">
                                                </a>
                                            </div>

                                            <div class="product-content">
                                                <h4 class="product-name">
                                                    <a href="<?= base_url('detalle_producto.php?id=' . urlencode($relId)) ?>">
                                                        <?= e($rel['nombre'] ?? 'Producto relacionado') ?>
                                                    </a>
                                                </h4>
                                                <span class="price">S/ <?= number_format((float) ($rel['precio'] ?? 0), 2) ?></span>

                                                <div class="product-actions">
                                                    <a href="<?= base_url('detalle_producto.php?id=' . urlencode($relId)) ?>"
                                                        class="action-icon view">
                                                        <i class="dl-icon-view"></i>
                                                    </a>
                                                    <a href="<?= base_url('carrito/agregar?id=' . urlencode($relId)) ?>"
                                                        class="action-icon add-cart">
                                                        <i class="dl-icon-cart29"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action$="carrito/agregar"]');
        if (!form) {
            return;
        }

        const colorSelect = form.querySelector('select[name="color"]');
        const tallaSelect = form.querySelector('select[name="talla"]');
        const selects = [];
        const mensajes = {
            color: 'Por favor selecciona un color',
            talla: 'Por favor selecciona una talla',
        };

        if (colorSelect) {
            selects.push(colorSelect);
        }

        if (tallaSelect) {
            selects.push(tallaSelect);
        }

        if (selects.length === 0) {
            return;
        }

        form.addEventListener('submit', function(event) {
            let isValid = true;
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            selects.forEach(select => select.classList.remove('input-error'));

            selects.forEach((select) => {
                if (!select.value) {
                    isValid = false;
                    showError(select, mensajes[select.name] || 'Por favor selecciona una opción');
                }
            });

            if (!isValid) {
                event.preventDefault();
                const firstError = form.querySelector('.input-error');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });

        function showError(input, message) {
            input.classList.add('input-error');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
    });
</script>