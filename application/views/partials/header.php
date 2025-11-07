<body>

    <div class="fixed-action-btn">
        <div class="hi-icon-wrap hi-icon-effect-8">
            <a
                href="https://api.whatsapp.com/send?phone=+51901110822&amp;text=Mi%20consulta%20es%20..."
                target="_blank"
                class="hi-icon hi-icon-archive"></a>
        </div>
        <div class="wslogo"><i class="fab fa-whatsapp wisco"
                aria-hidden="true"></i></div>
    </div>

    <div class="ai-preloader active">
        <div class="ai-preloader-inner h-100 d-flex align-items-center justify-content-center">
            <div class="ai-child ai-bounce1"></div>
            <div class="ai-child ai-bounce2"></div>
            <div class="ai-child ai-bounce3"></div>
        </div>
    </div>

    <div class="wrapper">
        <header class="header header-fullwidth header-style-4">
            <div class="top-bar d-none d-md-block">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="notice-text-wrapper">
                                <p class="notice-text"><span><strong>GRAN</strong>
                                        DESCUENTO</span> <strong>DEL 30%</strong> COMPRA AHORA, NO TE LO PIERDAS</p>
                                <a class="close-notice"><i
                                        class="dl-icon-close"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-outer">
                <div class="header-inner fixed-header">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-lg-2 col-md-3 col-4 order-1">
                                <div class="header-left d-flex">
                                    <a href="<?= base_url(); ?>" class="logo-box">
                                        <figure class="logo--normal">
                                            <img src="<?= asset_url('img/logo/logo.png'); ?>" alt="Logo" />
                                        </figure>
                                        <figure class="logo--transparency">
                                            <img src="<?= asset_url('img/logo/logo-white.png'); ?>" alt="Logo" />
                                        </figure>
                                    </a>
                                    <ul class="header-toolbar">
                                        <li class="header-toolbar__item d-none d-lg-block">
                                            <a href="#sideNav" class="toolbar-btn">
                                                <i class="dl-icon-menu2"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-8 order-3 order-lg-2">
                                <nav class="main-navigation">
<?php
$uri_actual = $_SERVER['REQUEST_URI'] ?? '';
?>
                                    <ul class="mainmenu mainmenu--centered">
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/novedades/') === false && strpos($uri_actual, '/productos') === false ? 'active' : '' ?>">
                                            <a href="http://localhost/novedades" class="mainmenu__link">
                                                <span class="mm-text">Inicio</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/productos') !== false ? 'active' : '' ?>">
                                            <a href="http://localhost/novedades/productos" class="mainmenu__link">
                                                <span class="mm-text">Tienda</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/novedades/novedades') !== false ? 'active' : '' ?>">
                                            <a href="http://localhost/novedades/novedades" class="mainmenu__link">
                                                <span class="mm-text">Novedades</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item ">
                                            <a href="http://localhost/novedades/ofertas" class="mainmenu__link">
                                                <span class="mm-text">Ofertas</span>
                                                <span class="tip">Hot</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/populares') !== false ? 'active' : '' ?>">
                                            <a href="http://localhost/novedades/populares" class="mainmenu__link">
                                                <span class="mm-text">Populares</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/por_mayor') !== false ? 'active' : '' ?>">
                                            <a href="http://localhost/novedades/por_mayor" class="mainmenu__link">
                                                <span class="mm-text">Por Mayor</span>
                                            </a>
                                        </li>
                                        <!--------
                                        <li class="mainmenu__item ">
                                            <a href="http://localhost/novedades/blog" class="mainmenu__link">
                                                <span class="mm-text">Blog</span>
                                            </a>
                                        </li>
                                        -------->
                                    </ul>
                                </nav>
                            </div>

                            <div class="col-lg-2 col-md-9 col-8 order-2 order-lg-3">
                                <ul class="header-toolbar text-end">
                                    <li class="header-toolbar__item user-info-menu-btn">
                                        <a href="#">
                                            <i class="fa fa-user-circle-o"></i>
                                        </a>
                                        <ul class="user-info-menu">
                                            <li>
                                                <a href="<?= base_url('mi-cuenta'); ?>">Mi cuenta</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('carrito'); ?>">Mi carrito</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('checkout'); ?>">Ir a pagar</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="header-toolbar__item">
                                        <a href="#miniCart" class="mini-cart-btn toolbar-btn">
                                            <i class="dl-icon-cart4"></i>
                                            <sup class="mini-cart-count">2</sup>
                                        </a>
                                    </li>
                                    <li class="header-toolbar__item">
                                        <a href="#searchForm" class="search-btn toolbar-btn">
                                            <i class="dl-icon-search1"></i>
                                        </a>
                                    </li>
                                    <li class="header-toolbar__item d-lg-none">
                                        <a href="#" class="menu-btn"></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-sticky-header-height"></div>
            </div>
        </header>

        <header class="header-mobile">
            <div class="header-mobile__outer">
                <div class="header-mobile__inner fixed-header">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <a href="<?= base_url(); ?>" class="logo-box">
                                    <figure class="logo--normal">
                                        <img src="<?= asset_url('img/logo/logo.png'); ?>" alt="Logo">
                                    </figure>
                                </a>
                            </div>
                            <div class="col-8">
                                <ul class="header-toolbar text-end">

                                    <li class="header-toolbar__item d-lg-block">
                                        <a href="#sideNav" class="toolbar-btn"></a>
                                        <i class="dl-icon-menu2 categoria_responsive"></i>
                                    </li>
                                    
                                    <li class="header-toolbar__item user-info-menu-btn">
                                        <a href="#">
                                            <i class="fa fa-user-circle-o"></i>
                                        </a>
                                        <ul class="user-info-menu">
                                            <li>
                                                <a href="<?= base_url('mi-cuenta'); ?>">Mi cuenta</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('carrito'); ?>">Mi carrito</a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('checkout'); ?>">Ir a pagar</a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="header-toolbar__item">
                                        <a href="#miniCart" class="mini-cart-btn toolbar-btn">
                                            <i class="dl-icon-cart4"></i>
                                            <sup class="mini-cart-count">2</sup>
                                        </a>
                                    </li>
                                    <li class="header-toolbar__item">
                                        <a href="#searchForm" class="search-btn toolbar-btn">
                                            <i class="dl-icon-search1"></i>
                                        </a>
                                    </li>
                                    <li class="header-toolbar__item d-lg-none">
                                        <a href="#sideNav" class="menu-btn"></a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mobile-navigation dl-menuwrapper" id="dl-menu">
                                    <button class="dl-trigger">Open Menu</button>
                                    <ul class="dl-menu">
                                        <li>
                                            <a href="http://localhost/novedades">
                                                Inicio
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://localhost/novedades/productos">
                                                Tienda
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://localhost/novedades/novedades">
                                                Novedades
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://localhost/novedades/ofertas">
                                                Ofertas
                                                <span class="tip">Hot</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://localhost/novedades/por_mayor">
                                                Por mayor
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://localhost/novedades/blog">
                                                Blog
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= base_url('para-el-cliente'); ?>">
                                                Contáctenos
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mobile-sticky-header-height"></div>
            </div>
        </header>