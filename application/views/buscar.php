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
            </div>
        </div>
    </div>
</div>