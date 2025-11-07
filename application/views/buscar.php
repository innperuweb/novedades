<?php
$query = isset($query) ? trim((string) $query) : '';
$resultados = isset($resultados) && is_array($resultados) ? $resultados : [];
$totalResultados = count($resultados);

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
                <div class="col-12" id="main-content">
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
                                            Ingresa un término en el buscador para encontrar productos.
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
                            <?php if ($totalResultados === 0): ?>
                                <div class="col-12 text-center">
                                    <?php if ($query === ''): ?>
                                        <p>Utiliza el campo de búsqueda para comenzar.</p>
                                    <?php else: ?>
                                        <p>No se encontraron productos.</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php foreach ($resultados as $p): ?>

                                    <?php
                                    $idProducto = (int) ($p['id'] ?? 0);
                                    $nombreProducto = e($p['nombre'] ?? 'Producto');
                                    $precio = number_format((float) ($p['precio'] ?? 0), 2);
                                    $detalleUrl = base_url('productos/detalle?id=' . $idProducto);

                                    // Buscar la imagen principal
                                    $imagenPrincipal = '';
                                    $rutaPrincipal = isset($p['ruta_principal']) ? trim((string) $p['ruta_principal']) : '';

                                    if ($rutaPrincipal !== '') {
                                        $normalizado = ltrim(str_replace('\\', '/', $rutaPrincipal), '/');
                                        if (strpos($normalizado, 'uploads/') === 0) {
                                            $rutaRelativa = 'public/assets/' . $normalizado;
                                            $rutaArchivo = rtrim(ROOT_PATH, '/\\') . '/public/assets/' . $normalizado;
                                            if (is_file($rutaArchivo)) {
                                                $imagenPrincipal = $rutaRelativa;
                                            }
                                        }
                                    }

                                    if ($imagenPrincipal === '') {
                                        $rutaBase = rtrim(ROOT_PATH, '/\\') . '/public/assets/uploads/productos/' . $idProducto;
                                        if (is_dir($rutaBase)) {
                                            $archivos = scandir($rutaBase);
                                            foreach ($archivos as $archivo) {
                                                if (preg_match('/^1_.*\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                                                    $imagenPrincipal = "public/assets/uploads/productos/{$idProducto}/{$archivo}";
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    if ($imagenPrincipal === '') {
                                        $imagenPrincipal = 'public/assets/img/no-image.jpg';
                                    }
                                    ?>

                                    <div class="col-xl-3 col-md-6 mb--40 mb-md--30">
                                        <div class="airi-product">
                                            <div class="product-inner">
                                                <figure class="product-image">
                                                    <div class="product-image--holder">
                                                        <a href="<?= e($detalleUrl); ?>">
                                                            <img src="<?= e(base_url($imagenPrincipal)); ?>" alt="<?= $nombreProducto; ?>">
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
                                                    <h3 class="product-title">
                                                        <a href="<?= e($detalleUrl); ?>"><?= $nombreProducto; ?></a>
                                                    </h3>
                                                    <span class="product-price-wrapper">
                                                        <span class="money">S/ <?= $precio; ?></span>
                                                    </span>
                                                </div>
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