<footer class="footer footer-1 bg--black ptb--60 celular">
    <div class="footer-top pb--40 pb-md--30">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-8 mb-md--30">
                    <div class="footer-widget">
                        <div class="textwidget">
                            <img src="<?= asset_url('img/logo/logo-white.png'); ?>" alt="Logo" class="mb--10"> <br> <br>
                            <ul class="social">
                                <li class="social__item">
                                    <a href="#" class="social__link color--white">
                                        <i class="fa fa-facebook"></i>
                                    </a>
                                </li>
                                <li class="social__item">
                                    <a href="#" class="social__link color--white">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>
                                <li class="social__item">
                                    <a href="#" class="social__link color--white">
                                        <i class="fa-brands fa-tiktok"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-md--30">
                    <div class="footer-widget">
                        <h3 class="widget-title">Para el cliente</h3>
                        <ul class="widget-menu">
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Preguntas frecuentes</a></li>
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Envíos a nivel nacional</a></li>
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Pedidos por mayor</a></li>
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Garantías</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-sm--30">
                    <div class="footer-widget">
                        <h3 class="widget-title">Importante</h3>
                        <ul class="widget-menu">
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Términos y condiciones</a></li>
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Políticas de privacidad</a></li>
                            <li><a href="<?= base_url('para-el-cliente'); ?>">Cambios y devoluciones</a></li>
                            <li><a href="<?= base_url('libro-de-reclamaciones'); ?>">Libro de reclamaciones</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">Información</h4>
                        <ul class="contact-info">
                            <li class="contact-info__item">
                                <i class="fa fa-phone"></i>
                                <span><a href="tel:+51901200822" class="contact-info__link">901 200 822</a></span>
                                <span><a href="tel:+51901110822" class="contact-info__link">901 110 822</a></span>
                            </li>
                            <li class="contact-info__item">
                                <i class="fa fa-envelope"></i>
                                <span><a href="mailto:ys@novedades.pe"
                                        class="contact-info__link">ys@novedades.pe</a></span>
                            </li>
                            <li class="contact-info__item">
                                <i class="fa fa-map-marker"></i>
                                <span style="color: #fff;">Lima - Perú</span>
                            </li>
                            <li>
                                <h5 class="color--white mb-sm--30" style="font-size: 16px;">Recibir boletines
                                    informativos </h5>
                                <form action="#" class="newsletter-form mc-form">
                                    <input type="email" name="newsletter_email" id="newsletter_email"
                                        class="newsletter-form__input input--2" placeholder="Escribe tu email">
                                    <button type="submit" class="newsletter-form__submit submit--2"><i
                                            class="dl-icon-right"></i></button>
                                </form>
                            </li>


                        </ul>
                        <br>
                        <div class="textwidget">
                            <img src="<?= asset_url('img/others/payments.png'); ?>" alt="Payment">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="copyright-text">&copy; NOVEDADES | LO NUEVO, LO MEJOR, LO TUYO</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="searchform__popup" id="searchForm">
    <a href="#" class="btn-close"><i class="dl-icon-close"></i></a>
    <div class="searchform__body">
        <p>¿Qué producto buscas?</p>
        <form class="searchform" action="<?= base_url('buscar') ?>" method="get">
            <input type="text" name="q" id="search" class="searchform__input" placeholder="Buscar producto...">
            <button type="submit" class="searchform__submit">
                <i class="dl-icon-search10"></i>
            </button>
        </form>
        <div id="resultados"></div>
    </div>
</div>

<aside class="side-navigation side-navigation--left" id="sideNav">
    <div class="side-navigation-wrapper">
        <a href="#" class="btn-close"><i class="dl-icon-close"></i></a>
        <div class="side-navigation-inner">
            <div class="widget">
                <ul class="sidenav-menu sidenav-menu--icons">
                    <?php foreach ($categorias as $cat): ?>
                        <?php
                            $subcategorias = $cat['subcategorias'] ?? [];
                            $tieneSubcategorias = !empty($subcategorias);
                            $categoriaNombre = $cat['nombre'] ?? '';
                        ?>
                        <li class="sidenav-item <?= $tieneSubcategorias ? 'has-submenu' : ''; ?>">
                            <a href="#" class="categoria-toggle">
                                <i class="dl-icon-folder2"></i> <?= e($categoriaNombre); ?>
                            </a>
                            <?php if ($tieneSubcategorias): ?>
                                <ul class="submenu">
                                    <?php foreach ($subcategorias as $sub): ?>
                                        <?php
                                            $subNombre = $sub['nombre'] ?? '';
                                            $subSlug = $sub['slug'] ?? '';
                                            $subcatUrl = base_url('productos?subcat=' . rawurlencode($subSlug));
                                        ?>
                                        <li>
                                            <a href="<?= e($subcatUrl); ?>">
                                                <?= e($subNombre); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <br><br>
            <div class="widget">
                <div class="text-widget">
                    <p>
                        <a href="tel:901200822">901 200 822</a> <a href="tel:901110822">901 110 822</a>
                        <a href="mailto:ys@novedades.pe">ys@novedades.pe</a> Lima - Perú
                    </p>
                </div>
            </div>

            <div class="widget">
                <div class="text-widget">
                    <ul class="social social-small">
                        <li class="social__item">
                            <a href="https://facebook.com/" class="social__link">
                                <i class="fa fa-facebook"></i>
                            </a>
                        </li>
                        <li class="social__item">
                            <a href="https://instagram.com/" class="social__link">
                                <i class="fa fa-instagram"></i>
                            </a>
                        </li>
                        <li class="social__item">
                            <a href="https://youtube.com/" class="social__link">
                                <i class="fa-brands fa-tiktok"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside>

<?php
$miniCartItems = isset($miniCartItems) && is_array($miniCartItems)
    ? $miniCartItems
    : (function_exists('get_cart_session')
        ? get_cart_session()
        : (isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) ? $_SESSION['carrito'] : [])
    );

if (!is_array($miniCartItems)) {
    $miniCartItems = [];
}

$miniCartSubtotal = 0.0;
?>

<aside class="mini-cart" id="miniCart">
    <div class="mini-cart-wrapper">
        <a href="#" class="btn-close"><i class="dl-icon-close"></i></a>
        <div class="mini-cart-inner">
            <h5 class="mini-cart__heading mb--40 mb-lg--30">Mis compras</h5>
            <div class="mini-cart__content">
                <?php if (!empty($miniCartItems)): ?>
                    <ul class="mini-cart__list">
                        <?php foreach ($miniCartItems as $item): ?>
                            <?php
                            $idProducto = (int) ($item['id'] ?? 0);
                            $nombre = e($item['nombre'] ?? 'Producto');
                            $precio = (float) ($item['precio'] ?? 0);
                            $cantidad = isset($item['cantidad']) && is_numeric($item['cantidad']) ? (int) $item['cantidad'] : 1;
                            $cantidad = $cantidad > 0 ? $cantidad : 1;
                            $subtotalItem = $precio * $cantidad;
                            $miniCartSubtotal += $subtotalItem;
                            $uid = (string) ($item['uid'] ?? '');
                            $removeQuery = $uid !== ''
                                ? 'uid=' . urlencode($uid)
                                : 'id=' . urlencode((string) $idProducto);
                            $detalleUrl = base_url('productos/detalle?id=' . $idProducto);
                            $imagenUrl = base_url('public/assets/img/no-image.jpg');
                            $imagenCampo = trim((string) ($item['imagen'] ?? ''));
                            $imagenRel = null;

                            if ($idProducto > 0) {
                                $directorio = __DIR__ . '/../../public/assets/uploads/productos/' . $idProducto;
                                if (is_dir($directorio)) {
                                    $archivos = scandir($directorio);
                                    if ($archivos !== false) {
                                        foreach ($archivos as $archivo) {
                                            if (preg_match('/^1_.*\\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                                                $imagenRel = 'public/assets/uploads/productos/' . $idProducto . '/' . $archivo;
                                                break;
                                            }
                                        }

                                        if ($imagenRel === null) {
                                            foreach ($archivos as $archivo) {
                                                if ($archivo === '.' || $archivo === '..') {
                                                    continue;
                                                }

                                                if (preg_match('/\\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                                                    $imagenRel = 'public/assets/uploads/productos/' . $idProducto . '/' . $archivo;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($imagenRel === null && $imagenCampo !== '') {
                                if (preg_match('~^https?://~i', $imagenCampo)) {
                                    $imagenUrl = $imagenCampo;
                                } else {
                                    $candidato = ltrim($imagenCampo, '/');
                                    $posibles = [];

                                    if (strpos($candidato, 'public/') === 0) {
                                        $posibles[] = $candidato;
                                    } elseif (strpos($candidato, 'assets/') === 0) {
                                        $posibles[] = 'public/' . $candidato;
                                    } elseif (strpos($candidato, 'uploads/') === 0) {
                                        $posibles[] = 'public/' . $candidato;
                                        $posibles[] = 'public/assets/' . $candidato;
                                    } else {
                                        if ($idProducto > 0) {
                                            $posibles[] = 'public/assets/uploads/productos/' . $idProducto . '/' . $candidato;
                                        }
                                        $posibles[] = 'public/assets/' . $candidato;
                                    }

                                    foreach ($posibles as $posible) {
                                        $rutaLocal = __DIR__ . '/../../' . $posible;
                                        if (is_file($rutaLocal)) {
                                            $imagenRel = $posible;
                                            break;
                                        }
                                    }

                                    if ($imagenRel === null && $candidato !== '' && $idProducto > 0) {
                                        $imagenRel = 'public/assets/uploads/productos/' . $idProducto . '/' . $candidato;
                                    }
                                }
                            }

                            if ($imagenRel !== null) {
                                $imagenUrl = base_url($imagenRel);
                            } elseif (!preg_match('~^https?://~i', $imagenUrl)) {
                                $imagenUrl = base_url('public/assets/img/no-image.jpg');
                            }
                            ?>
                            <li class="mini-cart__product">
                                <a href="<?= e(base_url('carrito/eliminar?' . $removeQuery)); ?>" class="remove-from-cart remove">
                                    <i class="dl-icon-close"></i>
                                </a>
                                <div class="mini-cart__product__image">
                                    <img src="<?= e($imagenUrl); ?>" alt="<?= $nombre; ?>">
                                </div>
                                <div class="mini-cart__product__content">
                                    <a class="mini-cart__product__title" href="<?= e($detalleUrl); ?>">
                                        <?= $nombre; ?>
                                    </a>
                                    <span class="mini-cart__product__quantity">
                                        <?= e((string) $cantidad); ?> x S/ <?= number_format($precio, 2); ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mini-cart__total">
                        <span>Subtotal</span>
                        <span class="ammount">S/ <?= number_format($miniCartSubtotal, 2); ?></span>
                    </div>

                    <div class="mini-cart__buttons">
                        <a href="<?= base_url('carrito'); ?>" class="btn btn-fullwidth btn-style-1">Ver carrito</a>
                    </div>
                <?php else: ?>
                    <p class="text-center">Tu carrito está vacío.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</aside>

<div class="ai-global-overlay"></div>

</div>

</body>

</html>