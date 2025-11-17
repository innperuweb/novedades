<div id="content" class="main-content-wrapper">

    <div class="homepage-slider" id="homepage-slider-1">
        <div id="rev_slider_7_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container" data-alias="home-12" data-source="gallery" style="margin:0px auto;background:transparent;padding:0px;margin-top:0px;margin-bottom:0px;">
            <div id="rev_slider_7_1" class="rev_slider fullwidthabanner" style="display:none;" data-version="5.4.7">
                <ul>

                    <?php if (!empty($sliders)): ?>
                        <?php foreach ($sliders as $i => $s): ?>

                            <li data-index="rs-<?= $s['id'] ?>" data-transition="random-premium" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="default" data-thumb="" data-rotate="0" data-saveperformance="off"
                                data-title="<?= sprintf('%02d', $i + 1); ?>" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                                <img src="<?= asset_url('img/slider/home-12/transparent.png'); ?>" data-bgcolor='#f8f7ee' style='background:#f8f7ee' alt="" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="off" class="rev-slidebg" data-no-retina>
                                <div class="tp-caption     rev_group" id="slide-15-layer-1" data-x="['left','left','center','center']" data-hoffset="['120','120','0','0']" data-y="['middle','middle','top','top']" data-voffset="['0','0','0','0']" data-width="['838','838','350','300']"
                                    data-height="['808','808','350','300']" data-whitespace="nowrap" data-type="group" data-responsive_offset="on" data-frames='[{"delay":10,"speed":300,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                                    data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]"
                                    data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]" style="z-index: 5; min-width: 838px; max-width: 838px; max-width: 808px; max-width: 808px; white-space: nowrap; font-size: 20px; line-height: 22px; font-weight: 400; color: #ffffff; letter-spacing: 0px;">
                                    <div class="tp-caption tp-shape tp-shapewrapper  tp-resizeme" id="slide-15-layer-2" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" data-width="['680','680','300','280']"
                                        data-height="['680','680','300','280']" data-whitespace="nowrap" data-type="shape" data-responsive_offset="on" data-frames='[{"delay":"+290","speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                        data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]"
                                        data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]" style="z-index: 6;background-color:rgb(239,236,213);border-radius:500px 500px 500px 500px;">
                                        <div class="rs-looped rs-wave" data-speed="10" data-angle="0" data-radius="10" data-origin="50% 50%"> </div>
                                    </div>
                                    <div class="tp-caption   tp-resizeme" id="slide-15-layer-3" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" data-width="none" data-height="none"
                                        data-whitespace="nowrap" data-type="image" data-responsive_offset="on" data-frames='[{"delay":"+690","speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                        data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]"
                                        data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]" style="z-index: 7;">
                                        <img src="<?= asset_url($s['imagen']) ?>" alt="" data-ww="['488','488','210px','180px']" data-hh="['712px','712px','306px','263px']" data-no-retina>
                                    </div>
                                </div>

                                <div class="tp-caption     rev_group" id="slide-15-layer-4" data-x="['right','right','center','center']" data-hoffset="['120','120','0','0']" data-y="['middle','middle','bottom','bottom']" data-voffset="['0','0','40','30']" data-width="['609','609','541','352']"
                                    data-height="['232','232','185','163']" data-whitespace="nowrap" data-type="group" data-responsive_offset="on" data-frames='[{"delay":10,"speed":300,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                                    data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]"
                                    data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]" style="z-index: 8; min-width: 609px; max-width: 609px; max-width: 232px; max-width: 232px; white-space: nowrap; font-size: 20px; line-height: 22px; font-weight: 400; color: #ffffff; letter-spacing: 0px;">

                                    <?php if (!empty($s['subtitulo'])): ?>
                                        <div class="tp-caption   tp-resizeme" id="slide-15-layer-5" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['91','91','67','50']" data-fontsize="['24','24','20','20']" data-lineheight="['29','29','24','24']"
                                            data-width="none" data-height="none" data-whitespace="nowrap" data-type="text" data-responsive_offset="on" data-frames='[{"delay":"+1490","speed":1500,"frame":"0","from":"x:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                            data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]"
                                            data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]" style="z-index: 9; white-space: nowrap; font-size: 24px; line-height: 29px; font-weight: 400; color: #282828; letter-spacing: 0px;font-family:Montserrat;">
                                            <?= htmlspecialchars($s['subtitulo']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="tp-caption   tp-resizeme" id="slide-15-layer-6" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['0','0','0','0']" data-fontsize="['78','78','60','40']" data-lineheight="['90','90','60','40']"
                                        data-width="none" data-height="none" data-whitespace="nowrap" data-type="text" data-responsive_offset="on" data-frames='[{"delay":"+1090","speed":1500,"frame":"0","from":"x:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                        data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]"
                                        data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]" style="z-index: 10; white-space: nowrap; font-size: 78px; line-height: 90px; font-weight: 400; color: #282828; letter-spacing: 0px;font-family:Montserrat;">
                                        <?= htmlspecialchars($s['titulo'] ?: 'Slide') ?> </div>

                                    <?php if (!empty($s['boton_url'])): ?>
                                        <a class="tp-caption LaBtnOutlineBlack rev-btn " href="<?= htmlspecialchars($s['boton_url']) ?>" <?= htmlspecialchars($s['boton_url']) ?> id="slide-15-layer-7" data-x="['center','center','center','center']" data-hoffset="['2','2','2','2']" data-y="['top','top','top','top']" data-voffset="['145','145','115','101']"
                                            data-width="none" data-height="none" data-whitespace="nowrap" data-type="button" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":"+1770","speed":2000,"frame":"0","from":"y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;","mask":"x:0px;y:[100%];s:inherit;e:inherit;","to":"o:1;","ease":"Power2.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"},{"frame":"hover","speed":"0","ease":"Linear.easeNone","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgb(255,255,255);bg:rgb(40,40,40);bw:2 2 2 2;"}]'
                                            data-margintop="[0,0,0,0]" data-marginright="[0,0,0,0]" data-marginbottom="[0,0,0,0]" data-marginleft="[0,0,0,0]" data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[15,15,15,12]" data-paddingright="[45,45,45,35]"
                                            data-paddingbottom="[15,15,15,12]" data-paddingleft="[45,45,45,35]" style="z-index: 11; white-space: nowrap; border-color:rgb(40,40,40);border-width:2px 2px 2px 2px;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;cursor:pointer;">Comprar ahora </a>
                                    <?php endif; ?>

                                </div>
                            </li>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <div class="tp-bannertimer tp-bottom" style="visibility: hidden !important;"></div>
            </div>
        </div>
    </div>



    <section class="method-area pt--40 pt-md--30 pb-md--55">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-md--30">
                    <div class="method-box method-box-2 text-center">
                        <img src="<?= asset_url('img/icons/icon-1.png'); ?>" alt="Icon">
                        <h4 class="mt--20">ENVÍOS</h4>
                        <p>A todo el Perú</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-md--30">
                    <div class="method-box method-box-2 text-center">
                        <img src="<?= asset_url('img/icons/icon-2.png'); ?>" alt="Icon">
                        <h4 class="mt--20">PROMOCIONES</h4>
                        <p>Los mejores precios</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-sm--30">
                    <div class="method-box method-box-2 text-center">
                        <img src="<?= asset_url('img/icons/icon-3.png'); ?>" alt="Icon">
                        <h4 class="mt--20">ENVIO EXPRESS</h4>
                        <p>Consulta ahora</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="method-box method-box-2 text-center">
                        <img src="<?= asset_url('img/icons/icon-4.png'); ?>" alt="Icon">
                        <h4 class="mt--20">PAGO 100% SEGURO</h4>
                        <p>Protege al comprador y su seguridad</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-products-area pt--70 pb--80 pt-md--20 pb-md--60">
        <div class="container-fluid p-0">
            <div class="row mb--30 mb-md--20">
                <div class="col-12 text-center">
                    <h2 class="heading-secondary">Categorías
                    </h2>
                </div>
            </div>
            <div class="row g-0">
                <div class="col-12">
                    <div class="airi-element-carousel product-carousel dot-style-1 dark-dot slick-dot-mb-40 slick-dot-mb-md-30" data-slick-options='{
                                    "spaceBetween": 30,
                                    "slidesToShow": 4,
                                    "slidesToScroll": 4,
                                    "autoplaySpeed": 5000,
                                    "speed": 1000,
                                    "dots": true,
                                    "infinite": true,
                                    "centerMode": true,
                                    "centerPadding": "20%"
                                }' data-slick-responsive='[
                                    {"breakpoint":991, "settings": {"slidesToShow": 1} }
                                ]'>
                        <?php if (($productosAleatorios ?? []) === []): ?>
                            <p class="text-center mb-0">No hay productos disponibles en este momento.</p>
                        <?php else: ?>
                            <?php foreach ($productosAleatorios as $producto): ?>
                                <?php
                                $productoId = (int) ($producto['id'] ?? 0);
                                $nombreProducto = (string) ($producto['nombre'] ?? '');
                                $detalleUrl = 'http://localhost/novedades/productos/detalle?id=' . $productoId;
                                $imagenPrincipal = url_imagen_producto($productoId, $producto['ruta_principal'] ?? null);
                                ?>
                                <div class="item">
                                    <div class="single-featured-product">
                                        <div class="banner-box banner-type-3 banner-hover-1">
                                            <div class="banner-inner">
                                                <div class="banner-image">
                                                    <img src="<?= e($imagenPrincipal); ?>" alt="<?= e($nombreProducto); ?>">
                                                </div>
                                                <div class="banner-info">
                                                    <p class="banner-title-1 lts-5"><?= e($nombreProducto); ?></p>
                                                </div>
                                                <a class="banner-link banner-overlay" href="<?= e($detalleUrl); ?>" tabindex="0">Ver producto</a>
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
    </section>

    <?php
    $novedades = $novedades ?? [];
    $ofertas = $ofertas ?? [];
    $populares = $populares ?? [];

    $obtenerImagenPrincipal = static function (array $producto): string {
        $productoId = (int) ($producto['id'] ?? 0);
        return url_imagen_producto($productoId, $producto['ruta_principal'] ?? null);
    };

    $obtenerDetalleProducto = static function (int $productoId): string {
        if ($productoId <= 0) {
            return 'http://localhost/novedades/productos';
        }

        return 'http://localhost/novedades/productos/detalle?id=' . $productoId;
    };

    $formatearPrecioProducto = static function ($precio): string {
        $valor = is_numeric($precio) ? (float) $precio : 0.0;

        return 'S/ ' . number_format($valor, 2, '.', '');
    };
    ?>

    <section class="product-carousel-area pt--70 pt-md--50 pb--75 pb-md--55">
        <div class="container-fluid">
            <div class="row mb--40 mb-md--25">
                <div class="col-12 text-center">
                    <h2 class="heading-secondary">Novedades</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="airi-element-carousel product-carousel dot-style-2 dot-center cs-mt--5" data-slick-options='{
                                    "spaceBetween": 60,
                                    "spaceBetween_xl": 30,
                                    "slidesToShow": 5,
                                    "slidesToScroll": 5,
                                    "autoplaySpeed": 5000,
                                    "speed": 1000,
                                    "dots": true
                                }' data-slick-responsive='[
                                    {"breakpoint":1200, "settings": {
                                        "slidesToShow": 3,
                                        "slidesToScroll": 3
                                    } },
                                    {"breakpoint":992, "settings": {
                                        "slidesToShow": 2,
                                        "slidesToScroll": 2
                                    } },
                                    {"breakpoint":576, "settings": {
                                        "slidesToShow": 1,
                                        "slidesToShow": 1
                                    } }
                                ]'>
                        <?php if ($novedades === []): ?>
                            <div class="text-center w-100">
                                <p class="mb-0">No hay productos de novedades disponibles en este momento.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($novedades as $producto): ?>
                                <?php
                                $productoId = (int) ($producto['id'] ?? 0);
                                $nombreProducto = trim((string) ($producto['nombre'] ?? ''));
                                $detalleUrl = $obtenerDetalleProducto($productoId);
                                $imagenPrincipal = $obtenerImagenPrincipal($producto);
                                $precioProducto = $formatearPrecioProducto($producto['precio'] ?? 0);
                                ?>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="<?= e($detalleUrl); ?>">
                                                    <img src="<?= e($imagenPrincipal); ?>" alt="<?= e($nombreProducto); ?>">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a href="<?= e($detalleUrl); ?>" class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal">
                                                            <i class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="<?= e($detalleUrl); ?>"><?= e($nombreProducto); ?></a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money"><?= e($precioProducto); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="product-carousel-area pt--70 pt-md--50 pb--75 pb-md--55">
        <div class="container-fluid">
            <div class="row mb--40 mb-md--25">
                <div class="col-12 text-center">
                    <h2 class="heading-secondary">Ofertas</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="airi-element-carousel product-carousel dot-style-2 dot-center cs-mt--5" data-slick-options='{
                                    "spaceBetween": 60,
                                    "spaceBetween_xl": 30,
                                    "slidesToShow": 5,
                                    "slidesToScroll": 5,
                                    "autoplaySpeed": 5000,
                                    "speed": 1000,
                                    "dots": true
                                }' data-slick-responsive='[
                                    {"breakpoint":1200, "settings": {
                                        "slidesToShow": 3,
                                        "slidesToScroll": 3
                                    } },
                                    {"breakpoint":992, "settings": {
                                        "slidesToShow": 2,
                                        "slidesToScroll": 2
                                    } },
                                    {"breakpoint":576, "settings": {
                                        "slidesToShow": 1,
                                        "slidesToShow": 1
                                    } }
                                ]'>
                        <?php if ($ofertas === []): ?>
                            <div class="text-center w-100">
                                <p class="mb-0">No hay productos en ofertas disponibles actualmente.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($ofertas as $producto): ?>
                                <?php
                                $productoId = (int) ($producto['id'] ?? 0);
                                $nombreProducto = trim((string) ($producto['nombre'] ?? ''));
                                $detalleUrl = $obtenerDetalleProducto($productoId);
                                $imagenPrincipal = $obtenerImagenPrincipal($producto);
                                $precioProducto = $formatearPrecioProducto($producto['precio'] ?? 0);
                                ?>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="<?= e($detalleUrl); ?>">
                                                    <img src="<?= e($imagenPrincipal); ?>" alt="<?= e($nombreProducto); ?>">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a href="<?= e($detalleUrl); ?>" class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <span class="product-badge hot">Sale</span>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="<?= e($detalleUrl); ?>"><?= e($nombreProducto); ?></a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money"><?= e($precioProducto); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="product-carousel-area pt--70 pt-md--50 pb--75 pb-md--55">
        <div class="container-fluid">
            <div class="row mb--40 mb-md--25">
                <div class="col-12 text-center">
                    <h2 class="heading-secondary">Populares</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="airi-element-carousel product-carousel dot-style-2 dot-center cs-mt--5" data-slick-options='{
                                    "spaceBetween": 60,
                                    "spaceBetween_xl": 30,
                                    "slidesToShow": 5,
                                    "slidesToScroll": 5,
                                    "autoplaySpeed": 5000,
                                    "speed": 1000,
                                    "dots": true
                                }' data-slick-responsive='[
                                    {"breakpoint":1200, "settings": {
                                        "slidesToShow": 3,
                                        "slidesToScroll": 3
                                    } },
                                    {"breakpoint":992, "settings": {
                                        "slidesToShow": 2,
                                        "slidesToScroll": 2
                                    } },
                                    {"breakpoint":576, "settings": {
                                        "slidesToShow": 1,
                                        "slidesToShow": 1
                                    } }
                                ]'>
                        <?php if ($populares === []): ?>
                            <div class="text-center w-100">
                                <p class="mb-0">No hay productos populares disponibles actualmente.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($populares as $producto): ?>
                                <?php
                                $productoId = (int) ($producto['id'] ?? 0);
                                $nombreProducto = trim((string) ($producto['nombre'] ?? ''));
                                $detalleUrl = $obtenerDetalleProducto($productoId);
                                $imagenPrincipal = $obtenerImagenPrincipal($producto);
                                $precioProducto = $formatearPrecioProducto($producto['precio'] ?? 0);
                                ?>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="<?= e($detalleUrl); ?>">
                                                    <img src="<?= e($imagenPrincipal); ?>" alt="<?= e($nombreProducto); ?>">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a href="<?= e($detalleUrl); ?>" class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="<?= e($detalleUrl); ?>"><?= e($nombreProducto); ?></a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money"><?= e($precioProducto); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $publicidad = $publicidad ?? null; ?>

    <div class="banner-area">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-lg-4 d-md-flex d-lg-block">
                    <div class="banner-box banner-type-7 banner-1 banner-hover-5">
                        <div class="banner-inner">
                            <div class="banner-image">
                                <?php if ($publicidad): ?>
                                    <img src="<?= base_url($publicidad['imagen']); ?>" alt="Banner">
                                <?php endif; ?>
                            </div>
                            <div class="banner-info">
                                <div class="banner-info--inner">
                                    <?php if ($publicidad): ?>
                                        <p class="banner-title-3 color--white"><?= htmlspecialchars($publicidad['titulo']); ?></p>
                                        <p class="banner-title-7 font-bold"><?= htmlspecialchars($publicidad['subtitulo']); ?></p>
                                        <p class="banner-title-8 color--white"><?= htmlspecialchars($publicidad['texto']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="banner-box banner-type-7 banner-2 banner-hover-5">
                        <div class="banner-inner">
                            <div class="banner-image">
                                <img src="<?= asset_url('img/banner/m12-banner2.jpg'); ?>" alt="Banner">
                            </div>
                            <div class="banner-info">
                                <div class="banner-info--inner">
                                    <p class="banner-title-3 color--white">Kid's Corner</p>
                                    <p class="banner-title-9 color--white">Flash Sale</p>
                                    <p class="banner-title-7">Off 20%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="banner-box banner-type-7 banner-3 banner-hover-5">
                        <div class="banner-inner">
                            <div class="banner-image">
                                <img src="<?= asset_url('img/banner/m12-banner3.jpg'); ?>" alt="Banner">
                            </div>
                            <div class="banner-info">
                                <div class="banner-info--inner">
                                    <p class="banner-title-8 color--white">Bed Room 2019</p>
                                    <p class="banner-title-7 font-bold">Off 30%</p>
                                    <p class="banner-title-3 color--white">From $25.99</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="banner-box banner-type-7 banner-4 banner-hover-5">
                        <div class="banner-inner">
                            <div class="banner-image">
                                <img src="<?= asset_url('img/banner/m12-banner4.jpg'); ?>" alt="Banner">
                            </div>
                            <div class="banner-info">
                                <div class="banner-info--inner">
                                    <p class="banner-title-8 color--white">Home Decoreation</p>
                                    <p class="banner-title-7 font-bold">Off 20%</p>
                                    <p class="banner-title-3 color--white">for all items</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="banner-box banner-type-7 banner-5 banner-hover-5">
                        <div class="banner-inner">
                            <div class="banner-image">
                                <img src="<?= asset_url('img/banner/m12-banner4.jpg'); ?>" alt="Banner">
                            </div>
                            <div class="banner-info">
                                <div class="banner-info--inner">
                                    <p class="banner-title-8 color--white">Plant for Your Home</p>
                                    <p class="banner-title-7 font-bold">Off 20%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="testimonial-area pt--70 pt-md--50 pb--80 pb-md--60">
        <div class="container">
            <div class="row justify-content-center mb--35 mb-md--30">
                <div class="col-lg-8 text-center">
                    <h2 class="heading-secondary font-bold text-uppercase">Experiencia de clientes</h2>
                    <hr class="separator separator-color-2 center mt--25 mb--30">
                </div>
            </div>
            <div class="row g-0">
                <div class="col-12">
                    <div class="airi-element-carousel testimonial-carousel" data-slick-options='{
                                    "spaceBetween": 30,
                                    "autoplay": true,
                                    "speed": 1000,
                                    "slidesToShow": 4,
                                    "slidesToScroll": 4
                                }' data-slick-responsive='[
                                    {"breakpoint":992, "settings": {
                                        "slidesToShow": 1,
                                        "slidesToScroll": 1
                                    } }
                                ]'>

                        <div class="testimonial testimonial-style-2">
                            <div class="testimonial__inner">
                                <p class="testimonial__desc">"Maecenas eu accumsan libero. Fusce id imperdiet felis. Cras sed ex vel.</p>
                                <div class="testimonial__author">
                                    <img src="<?= asset_url('img/others/happy-client-1.jpg'); ?>" alt="Author" class="testimonial__author--img">
                                    <h3 class="testimonial__author--name">Lura Frazier</h3>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial testimonial-style-2">
                            <div class="testimonial__inner">
                                <p class="testimonial__desc">"Maecenas eu accumsan libero. Fusce id imperdiet felis. Cras sed ex vel.</p>
                                <div class="testimonial__author">
                                    <img src="<?= asset_url('img/others/happy-client-1.jpg'); ?>" alt="Author" class="testimonial__author--img">
                                    <h3 class="testimonial__author--name">Lura Frazier</h3>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial testimonial-style-2">
                            <div class="testimonial__inner">
                                <p class="testimonial__desc">"Maecenas eu accumsan libero. Fusce id imperdiet felis. Cras sed ex vel.</p>
                                <div class="testimonial__author">
                                    <img src="<?= asset_url('img/others/happy-client-1.jpg'); ?>" alt="Author" class="testimonial__author--img">
                                    <h3 class="testimonial__author--name">Lura Frazier</h3>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial testimonial-style-2">
                            <div class="testimonial__inner">
                                <p class="testimonial__desc">"Maecenas eu accumsan libero. Fusce id imperdiet felis. Cras sed ex vel.</p>
                                <div class="testimonial__author">
                                    <img src="<?= asset_url('img/others/happy-client-1.jpg'); ?>" alt="Author" class="testimonial__author--img">
                                    <h3 class="testimonial__author--name">Lura Frazier</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


</div>