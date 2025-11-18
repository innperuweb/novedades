<?php
$query = isset($query) ? trim((string) $query) : '';
$resultados = isset($resultados) && is_array($resultados) ? $resultados : [];
$totalResultados = count($resultados);

$productos = isset($productos) && is_array($productos) ? $productos : $resultados;
$slug_subcat = isset($slug_subcat) ? (string) $slug_subcat : '';
$orden = isset($orden) ? (string) $orden : '';
$pagina_actual = isset($pagina_actual) ? max(1, (int) $pagina_actual) : 1;
$limite = isset($limite) ? max(1, (int) $limite) : 20;
$total_resultados = count($productos);

if ($total_resultados > 0) {
    $inicio = ($pagina_actual - 1) * $limite + 1;
    $fin = min($inicio + $limite - 1, $total_resultados);
} else {
    $inicio = 0;
    $fin = 0;
}

$min_precio = isset($min_precio) ? (float) $min_precio : 0.0;
$max_precio = isset($max_precio) ? (float) $max_precio : 10000.0;

$formatearPrecio = static fn(float $valor): string => number_format($valor, 2, '.', '');

$url_base_listado = isset($url_base_listado) ? trim((string) $url_base_listado) : 'buscar';
$url_base_listado = $url_base_listado !== '' ? ltrim($url_base_listado, '/') : 'buscar';

$urlBaseProductos = site_url($url_base_listado);
$construirUrlOrden = static function (string $ordenDeseado) use ($slug_subcat, $urlBaseProductos, $query): string {
    $params = [];

    if ($query !== '') {
        $params['q'] = $query;
    }
    if ($slug_subcat !== '') {
        $params['subcat'] = $slug_subcat;
    }
    if ($ordenDeseado !== '') {
        $params['order'] = $ordenDeseado;
    }
    $queryString = http_build_query($params);

    return $urlBaseProductos . ($queryString !== '' ? ('?' . $queryString) : '');
};

$generarUrlSubcategoria = static function (string $slug) use ($orden, $urlBaseProductos, $query): string {
    $params = ['subcat' => $slug];
    if ($orden !== '') {
        $params['order'] = $orden;
    }
    if ($query !== '') {
        $params['q'] = $query;
    }

    return $urlBaseProductos . '?' . http_build_query($params);
};

$formActionParametros = [];
if ($query !== '') {
    $formActionParametros['q'] = $query;
}
if ($slug_subcat !== '') {
    $formActionParametros['subcat'] = $slug_subcat;
}
if ($orden !== '') {
    $formActionParametros['order'] = $orden;
}

$formActionProductos = $urlBaseProductos . ($formActionParametros !== [] ? ('?' . http_build_query($formActionParametros)) : '');

$subcategorias = $subcategorias ?? [];
$montoVisual = 'S/ ' . $formatearPrecio($min_precio) . ' - S/ ' . $formatearPrecio($max_precio);

// Definir título de página dinámico
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
                                        <?php if ($total_resultados > 0): ?>
                                            <p class="product-pages">
                                                Cantidad <?= e((string) $inicio); ?>–<?= e((string) $fin); ?> de <?= e((string) $total_resultados); ?> resultados
                                            </p>
                                        <?php else: ?>
                                            <p class="product-pages">Cantidad 0 resultados</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="shop-toolbar__right">
                                        <div class="product-ordering">
                                            <a href="#" class="product-ordering__btn shop-toolbar__btn">
                                                <span>Recomendación</span>
                                                <i></i>
                                            </a>
                                            <ul class="product-ordering__list">
                                                <li class="<?= $orden === 'precio_asc' ? 'active' : ''; ?>">
                                                    <a href="<?= e($construirUrlOrden('precio_asc')); ?>">Precio menor a mayor</a>
                                                </li>
                                                <li class="<?= $orden === 'precio_desc' ? 'active' : ''; ?>">
                                                    <a href="<?= e($construirUrlOrden('precio_desc')); ?>">Precio mayor a menor</a>
                                                </li>
                                                <li class="<?= $orden === 'nombre_asc' ? 'active' : ''; ?>">
                                                    <a href="<?= e($construirUrlOrden('nombre_asc')); ?>">Ordenar A - Z</a>
                                                </li>
                                                <li class="<?= $orden === 'nombre_desc' ? 'active' : ''; ?>">
                                                    <a href="<?= e($construirUrlOrden('nombre_desc')); ?>">Ordenar Z - A</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shop-toolbar mb--20">
                        <div class="shop-toolbar__inner">
                            <div class="row align-items-center">
                                <div class="col-12 text-center">
                                    <?php if ($totalResultados > 0): ?>
                                        <p class="product-pages">
                                            Se encontraron <?= e((string)$totalResultados); ?> producto(s).
                                        </p>
                                    <?php elseif ($query === ''): ?>
                                        <p class="product-pages">
                                            Ingresa un término en el buscador…
                                        </p>
                                    <?php else: ?>
                                        <p class="product-pages">
                                            No se encontraron productos.
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="shop-products">
                        <div class="row grid-space-30">
                            <?php if (empty($resultados)): ?>
                                <div class="col-12 text-center">
                                    <p>No se encontraron productos.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($resultados as $p): ?>
                                    <?php
                                    $id = (int)$p['id'];
                                    $nombre = e($p['nombre'] ?? '');
                                    $precio = number_format((float)$p['precio'] ?? 0, 2);
                                    $detalleUrl = base_url('productos/detalle?id=' . $id);
                                    $imagen = 'public/assets/img/no-image.jpg';

                                    $dir = __DIR__ . '/../../public/assets/uploads/productos/' . $id;
                                    if (is_dir($dir)) {
                                        foreach (scandir($dir) as $f) {
                                            if (preg_match('/^1_.*\.(jpg|jpeg|png|webp)$/i', $f)) {
                                                $imagen = 'public/assets/uploads/productos/' . $id . '/' . $f;
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="col-xl-3 col-md-6 mb--40 mb-md--30">
                                        <div class="airi-product">
                                            <div class="product-inner">
                                                <figure class="product-image">
                                                    <div class="product-image--holder">
                                                        <a href="<?= e($detalleUrl); ?>">
                                                            <img src="<?= e(base_url($imagen)); ?>" alt="<?= $nombre; ?>">
                                                        </a>
                                                    </div>
                                                </figure>
                                                <div class="product-info text-center">
                                                    <h3 class="product-title"><?= $nombre; ?></h3>
                                                    <span class="product-price">S/ <?= $precio; ?></span>
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

                        <!---------
                        <div class="product-widget categroy-widget mb--35 mb-md--30">
                            <h3 class="widget-title">Categorías</h3>
                            <ul class="prouduct-categories product-widget__list">
                                <?php if ($subcategorias !== []): ?>
                                    <?php foreach ($subcategorias as $subcategoria): ?>
                                        <?php
                                        $slug = (string) ($subcategoria['slug'] ?? '');
                                        $nombre = (string) ($subcategoria['nombre'] ?? '');
                                        $esActual = $slug_subcat !== '' && $slug_subcat === $slug;
                                        $clase = $esActual ? ' class="active"' : '';
                                        $urlSub = $slug !== '' ? $generarUrlSubcategoria($slug) : $urlBaseProductos;
                                        ?>
                                        <li<?= $clase; ?>><a href="<?= e($urlSub); ?>"><?= e($nombre); ?></a></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><span>No hay subcategorías disponibles.</span></li>
                                    <?php endif; ?>
                            </ul>
                        </div>
                        ---------->

                        <div class="product-widget product-price-widget mb--40 mb-md--35">
                            <h3 class="widget-title">Precio</h3>
                            <div class="widget_content">
                                <form action="<?= e($formActionProductos); ?>" method="post" class="price-filter-form">
                                    <div id="slider-range" class="price-slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
                                        data-min-default="0" data-max-default="10000"
                                        data-min="<?= e($formatearPrecio($min_precio)); ?>" data-max="<?= e($formatearPrecio($max_precio)); ?>">
                                        <div class="ui-slider-range ui-widget-header ui-corner-all"></div>
                                        <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0"></span>
                                        <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0"></span>
                                    </div>
                                    <input type="hidden" name="min_precio" id="min_precio" value="<?= e($formatearPrecio($min_precio)); ?>">
                                    <input type="hidden" name="max_precio" id="max_precio" value="<?= e($formatearPrecio($max_precio)); ?>">
                                    <div class="filter-price">
                                        <div class="filter-price__count">
                                            <div class="filter-price__input-group mb--20">
                                                <span>Precio: </span>
                                                <input type="text" id="amount" class="amount-range" readonly value="<?= e($montoVisual); ?>">
                                            </div>
                                            <button type="submit" class="btn btn-style-1 sidebar-btn">
                                                Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>