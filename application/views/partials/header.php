<?php
require_once APP_PATH . '/models/InformacionModel.php';

$infoContacto = InformacionModel::obtenerPorTipo('contacto') ?? [];
$infoRedes = InformacionModel::obtenerPorTipo('redes') ?? [];
$infoHeader = InformacionModel::obtenerPorTipo('header') ?? [];

$infoContacto += [
    'telefono1' => '',
    'telefono2' => '',
    'email'     => '',
];

$infoRedes += [
    'facebook'  => '',
    'instagram' => '',
    'youtube'   => '',
    'tiktok'    => '',
];

$infoHeader += [
    'mensaje_header' => '',
];

$miniCartItems = isset($miniCartItems) && is_array($miniCartItems)
    ? $miniCartItems
    : (function_exists('get_cart_session')
        ? get_cart_session()
        : (isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) ? $_SESSION['carrito'] : [])
    );

if (!is_array($miniCartItems)) {
    $miniCartItems = [];
}

$miniCartCount = 0;
foreach ($miniCartItems as $miniCartItem) {
    $cantidadItem = isset($miniCartItem['cantidad']) && is_numeric($miniCartItem['cantidad'])
        ? (int) $miniCartItem['cantidad']
        : 1;

    $miniCartCount += max(1, $cantidadItem);
}
?>

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
                                <p class="notice-text"><?= e($infoHeader['mensaje_header']); ?></p>
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
                                            <a href="<?= base_url(); ?>" class="mainmenu__link">
                                                <span class="mm-text">Inicio</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/productos') !== false ? 'active' : '' ?>">
                                            <a href="<?= base_url('productos'); ?>" class="mainmenu__link">
                                                <span class="mm-text">Tienda</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/novedades/novedades') !== false ? 'active' : '' ?>">
                                            <a href="<?= base_url('novedades'); ?>" class="mainmenu__link">
                                                <span class="mm-text">Novedades</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item ">
                                            <a href="<?= base_url('ofertas'); ?>" class="mainmenu__link">
                                                <span class="mm-text">Ofertas</span>
                                                <span class="tip">Hot</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/populares') !== false ? 'active' : '' ?>">
                                            <a href="<?= base_url('populares'); ?>" class="mainmenu__link">
                                                <span class="mm-text">Populares</span>
                                            </a>
                                        </li>
                                        <li class="mainmenu__item <?= strpos($uri_actual, '/por_mayor') !== false ? 'active' : '' ?>">
                                            <a href="<?= base_url('por_mayor'); ?>" class="mainmenu__link">
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
                                            <sup class="mini-cart-count"><?= $miniCartCount ?></sup>
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

        <div class="searchform__popup" id="searchForm">
            <a href="#" class="btn-close"><i class="dl-icon-close"></i></a>
            <div class="searchform__body">
                <p>¿Qué producto buscas?</p>
                <form class="searchform" action="<?= base_url('buscar'); ?>" method="get">
                    <input
                        type="text"
                        name="q"
                        id="search"
                        class="searchform__input"
                        placeholder="Buscar productos..."
                        required
                        aria-label="Buscar productos">
                    <button type="submit" class="searchform__submit">
                        <i class="dl-icon-search10"></i>
                    </button>
                </form>
            </div>
        </div>

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
                                            <sup class="mini-cart-count"><?= $miniCartCount ?></sup>
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchBtn = document.querySelector('.search-btn');
                const searchInput = document.querySelector('#search');

                if (searchBtn && searchInput) {
                    searchBtn.addEventListener('click', function() {
                        setTimeout(() => searchInput.focus(), 150);
                    });
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchForm = document.getElementById('searchForm');
                const searchBtn = document.querySelector('.search-btn');
                const closeBtn = document.querySelector('.btn-close');
                const searchInput = document.getElementById('search');

                // Abrir buscador
                if (searchBtn && searchForm) {
                    searchBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        searchForm.classList.add('open');
                        setTimeout(() => searchInput && searchInput.focus(), 150);
                    });
                }

                // Cerrar al hacer clic en el botón de cerrar
                if (closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        searchForm.classList.remove('open');
                    });
                }

                // Cerrar con la tecla ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && searchForm.classList.contains('open')) {
                        searchForm.classList.remove('open');
                    }
                });

                // Cerrar si hace clic fuera del formulario
                searchForm.addEventListener('click', function(e) {
                    const isBody = e.target.classList.contains('searchform__body');
                    const isPopup = e.target.id === 'searchForm';
                    if (isBody || isPopup) {
                        searchForm.classList.remove('open');
                    }
                });
            });
        </script>