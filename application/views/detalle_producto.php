

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
                                <div class="product-gallery__thumb--image">
                                    <div class="airi-element-carousel nav-slider slick-vertical" data-slick-options='{
                                                "slidesToShow": 3,
                                                "slidesToScroll": 1,
                                                "vertical": true,
                                                "swipe": true,
                                                "verticalSwiping": true,
                                                "infinite": true,
                                                "focusOnSelect": true,
                                                "asNavFor": ".main-slider",
                                                "arrows": true, 
                                                "prevArrow": {"buttonClass": "slick-btn slick-prev", "iconClass": "fa fa-angle-up" },
                                                "nextArrow": {"buttonClass": "slick-btn slick-next", "iconClass": "fa fa-angle-down" }
                                            }' data-slick-responsive='[
                                                {
                                                    "breakpoint":992, 
                                                    "settings": {
                                                        "slidesToShow": 4,
                                                        "vertical": false,
                                                        "verticalSwiping": false
                                                    } 
                                                },
                                                {
                                                    "breakpoint":575, 
                                                    "settings": {
                                                        "slidesToShow": 3,
                                                        "vertical": false,
                                                        "verticalSwiping": false
                                                    } 
                                                },
                                                {
                                                    "breakpoint":480, 
                                                    "settings": {
                                                        "slidesToShow": 2,
                                                        "vertical": false,
                                                        "verticalSwiping": false
                                                    } 
                                                }
                                            ]'>
                                        <figure class="product-gallery__thumb--single">
                                            <img src="<?= asset_url('img/products/prod-19-1-2.jpg'); ?>" alt="Products">
                                        </figure>
                                        <figure class="product-gallery__thumb--single">
                                            <img src="<?= asset_url('img/products/prod-19-2-2.jpg'); ?>" alt="Products">
                                        </figure>
                                        <figure class="product-gallery__thumb--single">
                                            <img src="<?= asset_url('img/products/prod-19-3-2.jpg'); ?>" alt="Products">
                                        </figure>
                                        <figure class="product-gallery__thumb--single">
                                            <img src="<?= asset_url('img/products/prod-19-4-2.jpg'); ?>" alt="Products">
                                        </figure>
                                    </div>
                                </div>
                            </div>
                            <div class="product-gallery__large-image">
                                <div class="gallery-with-thumbs">
                                    <div class="product-gallery__wrapper">
                                        <div class="airi-element-carousel main-slider product-gallery__full-image image-popup" data-slick-options='{
                                                    "slidesToShow": 1,
                                                    "slidesToScroll": 1,
                                                    "infinite": true,
                                                    "arrows": false, 
                                                    "asNavFor": ".nav-slider"
                                                }'>
                                            <figure class="product-gallery__image zoom">
                                                <img src="<?= asset_url('img/products/prod-19-1-big.jpg'); ?>" alt="Product">
                                            </figure>
                                            <figure class="product-gallery__image zoom">
                                                <img src="<?= asset_url('img/products/prod-19-2-big.jpg'); ?>" alt="Product">
                                            </figure>
                                            <figure class="product-gallery__image zoom">
                                                <img src="<?= asset_url('img/products/prod-19-3-big.jpg'); ?>" alt="Product">
                                            </figure>
                                            <figure class="product-gallery__image zoom">
                                                <img src="<?= asset_url('img/products/prod-19-4-big.jpg'); ?>" alt="Product">
                                            </figure>
                                        </div>
                                        <div class="product-gallery__actions">
                                            <button class="action-btn btn-zoom-popup"><i
                                                    class="dl-icon-zoom-in"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="product-badge new">New</span>
                    </div>
                </div>
                <div class="col-md-6 product-main-details mt-md--10 mt-sm--30">
                    <div class="product-summary">
                        <div class="clearfix"></div>
                        <span class="sku_wrapper font-size-12">MARCA: <span class="sku">SONY</span></span>
                        <h3 class="product-title">Waxed-effect pleated skirt</h3>

                        <div class="stock-row">
                            <span class="product-stock in-stock">
                                <i class="dl-icon-check-circle1"></i> Con stock
                            </span>

                            <span class="product-stock-red in-stock">
                                <i class="dl-icon-check-circle1"></i> Sin stock
                            </span>
                        </div>

                        <div class="product-price-wrapper mb--40 mb-md--10">
                            <span class="money">S/ 149.00</span>
                            <span class="old-price">
                                <span class="money">S/ 260.00</span>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <p class="product-short-description mb--45 mb-sm--20">Donec accumsan auctor iaculis. Sed suscipit arcu ligula, at egestas magna molestie a. Proin ac ex maximus, ultrices justo eget, sodales orci. Aliquam egestas libero ac turpis pharetra, in vehicula lacus scelerisque. Vestibulum
                            ut sem laoreet, feugiat tellus at, hendrerit.</p>

                        <div class="product-gallery__large-image">
                            <div class="gallery-with-thumbs">
                                <div class="product-gallery__wrapper">
                                    <div class="product-gallery__actions" style="left: 0px;">
                                        <a href="<?= asset_url('img/tallas.jpg'); ?>"
                                            class="action-btn video-popup">
                                            <i class="fa-solid fa-ruler ubicacion"> </i> <span class="tabla">Tabla de tallas</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>

                        <form action="#" class="variation-form mb--35">
                            <div class="product-color-variations mb--20">
                                <p class="swatch-label">Color: <strong class="swatch-label"></strong></p>
                                <div class="product-color-swatch variation-wrapper">
                                    <div class="swatch-wrapper">
                                        <a class="product-color-swatch-btn variation-btn blue" data-bs-toggle="tooltip" data-bs-placement="left" title="Blue">
                                            <span class="product-color-swatch-label">Blue</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-color-swatch-btn variation-btn green" data-bs-toggle="tooltip" data-bs-placement="left" title="Green">
                                            <span class="product-color-swatch-label">Green</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-color-swatch-btn variation-btn pink" data-bs-toggle="tooltip" data-bs-placement="left" title="Pink">
                                            <span class="product-color-swatch-label">Pink</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-color-swatch-btn variation-btn red" data-bs-toggle="tooltip" data-bs-placement="left" title="Red">
                                            <span class="product-color-swatch-label">Red</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-color-swatch-btn variation-btn white" data-bs-toggle="tooltip" data-bs-placement="left" title="White">
                                            <span class="product-color-swatch-label">white</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="product-size-variations">
                                <p class="swatch-label">Tallas: <strong class="swatch-label"></strong></p>
                                <div class="product-size-swatch variation-wrapper">
                                    <div class="swatch-wrapper">
                                        <a class="product-size-swatch-btn variation-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="L">
                                            <span class="product-size-swatch-label">L</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-size-swatch-btn variation-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="M">
                                            <span class="product-size-swatch-label">M</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-size-swatch-btn variation-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="S">
                                            <span class="product-size-swatch-label">S</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-size-swatch-btn variation-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="XL">
                                            <span class="product-size-swatch-label">XL</span>
                                        </a>
                                    </div>
                                    <div class="swatch-wrapper">
                                        <a class="product-size-swatch-btn variation-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="XXL">
                                            <span class="product-size-swatch-label">XXL</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="reset_variations">Clear</a>
                        </form>

                        <div class="form--action mb--30 mb-sm--20">
                            <div class="product-action flex-row align-items-center">
                                <div class="quantity">
                                    <input type="number" class="quantity-input" name="qty" id="qty" value="1" min="1">
                                </div>
                                <form method="POST" action="<?= base_url('carrito/agregar') ?>" class="form-add-cart">
                                    <input type="hidden" name="id" value="<?= $producto['id'] ?? 1 ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="btn btn-style-1 btn-large">
                                        Agregar al carrito
                                    </button>
                                </form>
                                <button type="button" class="btn btn-style-1 btn-large">
                                    Comprar ahora
                                </button>
                            </div>
                        </div>
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
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="#">
                                                    <img src="<?= asset_url('img/products/prod-18-2.jpg'); ?>" alt="Product Image" class="primary-image">
                                                    <img src="<?= asset_url('img/products/prod-18-1-big.jpg'); ?>" alt="Product Image" class="secondary-image">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i
                                                                class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="#">Blusa invierno de seda</a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money">S/ 149.00</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="#">
                                                    <img src="<?= asset_url('img/products/prod-18-2.jpg'); ?>" alt="Product Image" class="primary-image">
                                                    <img src="<?= asset_url('img/products/prod-18-1-big.jpg'); ?>" alt="Product Image" class="secondary-image">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i
                                                                class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="#">Blusa invierno de seda</a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money">S/ 149.00</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="#">
                                                    <img src="<?= asset_url('img/products/prod-18-2.jpg'); ?>" alt="Product Image" class="primary-image">
                                                    <img src="<?= asset_url('img/products/prod-18-1-big.jpg'); ?>" alt="Product Image" class="secondary-image">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i
                                                                class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="#">Blusa invierno de seda</a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money">S/ 149.00</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="#">
                                                    <img src="<?= asset_url('img/products/prod-18-2.jpg'); ?>" alt="Product Image" class="primary-image">
                                                    <img src="<?= asset_url('img/products/prod-18-1-big.jpg'); ?>" alt="Product Image" class="secondary-image">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i
                                                                class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="#">Blusa invierno de seda</a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money">S/ 149.00</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="airi-product">
                                    <div class="product-inner">
                                        <figure class="product-image">
                                            <div class="product-image--holder">
                                                <a href="#">
                                                    <img src="<?= asset_url('img/products/prod-18-2.jpg'); ?>" alt="Product Image" class="primary-image">
                                                    <img src="<?= asset_url('img/products/prod-18-1-big.jpg'); ?>" alt="Product Image" class="secondary-image">
                                                </a>
                                            </div>
                                            <div class="airi-product-action">
                                                <div class="product-action">
                                                    <a class="quickview-btn action-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver">
                                                        <span data-bs-toggle="modal" data-bs-target="#productModal">
                                                            <i
                                                                class="dl-icon-view"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </figure>
                                        <div class="product-info">
                                            <h3 class="product-title">
                                                <a href="#">Blusa invierno de seda</a>
                                            </h3>
                                            <span class="product-price-wrapper">
                                                <span class="money">S/ 149.00</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



    </div>
</div>
</div>

