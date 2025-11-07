<?php
$query = isset($query) ? trim((string) $query) : '';
$resultados = isset($resultados) && is_array($resultados) ? $resultados : [];
$totalResultados = count($resultados);

if ($totalResultados > 0) {
    $inicio = 1;
    $fin = $totalResultados;
} else {
    $inicio = 0;
    $fin = 0;
}

$tituloPagina = $query === ''
    ? 'Busca productos'
    : 'Resultados para: ' . $query;
?>
<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title"><?= e($tituloPagina); ?></h1>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper">
    <div class="page-content-inner enable-page-sidebar">
        <div class="container-fluid">
            <div class="row shop-sidebar pt--45 pt-md--35 pt-sm--20 pb--60 pb-md--50 pb-sm--40">
                <div class="col-lg-9 order-lg-2" id="main-content">
                    <div class="shop-toolbar">
                        <div class="shop-toolbar__inner">
                            <div class="row align-items-center">
                                <div class="col-md-6 text-md-start text-center mb-sm--20">
                                    <div class="shop-toolbar__left">
                                        <?php if ($totalResultados > 0): ?>
                                            <p class="product-pages">
                                                Cantidad <?= e((string) $inicio); ?>–<?= e((string) $fin); ?> de <?= e((string) $totalResultados); ?> resultados
                                            </p>
                                        <?php else: ?>
                                            <p class="product-pages">Cantidad 0 resultados</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end text-center">
                                    <div class="shop-toolbar__right">
                                        <div class="product-ordering">
                                            <span class="product-ordering__btn shop-toolbar__btn">
                                                <?= $query === '' ? 'Busca tus productos favoritos' : 'Resultados de tu búsqueda'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shop-products">
                        <div class="row grid-space-30">
                            <?php if ($resultados === []): ?>
                                <div class="col-12">
                                    <p class="text-center">
                                        <?= $query === '' ? 'Utiliza el buscador para encontrar productos.' : 'No se encontraron productos.'; ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($resultados as $p): ?>
                                    <div class="col-xl-3 col-md-6 mb--40 mb-md--30">
                                        <div class="airi-product">
                                            <div class="product-inner">
                                                <figure class="product-image">
                                                    <?php
                                                    $idProducto = isset($p['id']) ? (int) $p['id'] : 0;
                                                    $directorio = __DIR__ . '/../../public/assets/uploads/productos/' . $idProducto;

                                                    $imagenPrincipal = null;
                                                    if ($idProducto > 0 && is_dir($directorio)) {
                                                        $archivos = scandir($directorio);
                                                        foreach ($archivos as $archivo) {
                                                            if (preg_match('/^1_.*\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                                                                $imagenPrincipal = "public/assets/uploads/productos/{$idProducto}/{$archivo}";
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    if ($imagenPrincipal === null) {
                                                        $imagen = isset($p['imagen']) ? trim((string) $p['imagen']) : '';
                                                        if ($imagen !== '') {
                                                            $rutaAlterna = "public/assets/uploads/productos/{$idProducto}/1_{$imagen}";
                                                            if (is_file(__DIR__ . '/../../' . $rutaAlterna)) {
                                                                $imagenPrincipal = $rutaAlterna;
                                                            }
                                                        }
                                                    }

                                                    if ($imagenPrincipal === null) {
                                                        $imagenPrincipal = 'public/assets/img/no-image.jpg';
                                                    }

                                                    $detalleUrl = base_url('productos/detalle?id=' . $idProducto);
                                                    ?>
                                                    <div class="product-image--holder">
                                                        <a href="<?= e($detalleUrl); ?>">
                                                            <img src="<?= e($imagenPrincipal); ?>" alt="<?= e((string) ($p['nombre'] ?? 'Producto')); ?>">
                                                        </a>
                                                    </div>

                                                    <div class="airi-product-action">
                                                        <div class="product-action">
                                                            <a href="<?= e($detalleUrl); ?>"
                                                                class="quickview-btn action-btn"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="left"
                                                                title="Ver producto">
                                                                <i class="dl-icon-view"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </figure>
                                                <div class="product-info text-center">
                                                    <h3 class="product-title"><?= e((string) ($p['nombre'] ?? 'Producto')); ?></h3>
                                                    <span class="product-price">S/ <?= number_format((float) ($p['precio'] ?? 0), 2); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 order-lg-1 mt--30 mt-md--40" id="primary-sidebar">
                    <div class="sidebar-widget">
                        <div class="product-widget categroy-widget mb--35 mb-md--30">
                            <h3 class="widget-title">Búsquedas recientes</h3>
                            <ul class="prouduct-categories product-widget__list">
                                <li class="product-cat">
                                    <span><?= $query === '' ? 'Empieza a escribir para ver resultados.' : e($query); ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="product-widget price-filter-widget">
                            <h3 class="widget-title">Consejo</h3>
                            <p>Ingresa al menos dos letras o parte del código del producto para obtener coincidencias precisas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
