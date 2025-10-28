<?php include 'vistas/head.php'; ?>
<?php include 'vistas/header.php'; ?>

<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Carrito de compras</h1>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper">
    <div class="page-content-inner">
        <div class="container">
            <div class="row pt--80 pb--80 pt-md--45 pt-sm--25 pb-md--60 pb-sm--40">
                <div class="col-lg-8 mb-md--30">
                    <form class="cart-form" action="#">
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="table-content table-responsive">
                                    <table class="table text-center">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                                <th class="text-start">Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="product-remove text-start"><a href="#"><i
                                                            class="dl-icon-close"></i></a></td>
                                                <td class="product-thumbnail text-start">
                                                    <img src="img/products/prod-14-2-70x81.jpg" alt="Product Thumnail">
                                                </td>
                                                <td class="product-name text-start wide-column">
                                                    <h3>
                                                        <a href="product-details.html">Super skinny blazer</a>
                                                    </h3>
                                                </td>
                                                <td class="product-price">
                                                    <span class="product-price-wrapper">
                                                        <span class="money">$49.00</span>
                                                    </span>
                                                </td>
                                                
                                                <td class="product-quantity">
                                                    <div class="quantity">
                                                        <input type="number" class="quantity-input" name="qty" id="qty-1" value="1" min="1">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="product-remove text-start"><a href="#"><i
                                                            class="dl-icon-close"></i></a></td>
                                                <td class="product-thumbnail text-start">
                                                    <img src="img/products/prod-9-1-70x81.jpg" alt="Product Thumnail">
                                                </td>
                                                <td class="product-name text-start wide-column">
                                                    <h3>
                                                        <a href="product-details.html"> Jogging trousers</a>
                                                    </h3>
                                                </td>
                                                <td class="product-price">
                                                    <span class="product-price-wrapper">
                                                        <span class="money">$49.00</span>
                                                    </span>
                                                </td>
                                                <td class="product-quantity">
                                                    <div class="quantity">
                                                        <input type="number" class="quantity-input" name="qty" id="qty-2" value="1" min="1">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="product-remove text-start"><a href="#"><i
                                                            class="dl-icon-close"></i></a></td>
                                                <td class="product-thumbnail text-start">
                                                    <img src="img/products/prod-10-1-70x81.jpg" alt="Product Thumnail">
                                                </td>
                                                <td class="product-name text-start wide-column">
                                                    <h3>
                                                        <a href="product-details.html"> Grey blue leather
                                                            backpack</a>
                                                    </h3>
                                                </td>
                                                <td class="product-price">
                                                    <span class="product-price-wrapper">
                                                        <span class="money">$49.00</span>
                                                    </span>
                                                </td>
                                                <td class="product-quantity">
                                                    <div class="quantity">
                                                        <input type="number" class="quantity-input" name="qty" id="qty-3" value="1" min="1">
                                                    </div>
                                                </td>                                               
                                            </tr>
                                            <tr>
                                                <td class="product-remove text-start"><a href="#"><i
                                                            class="dl-icon-close"></i></a></td>
                                                <td class="product-thumbnail text-start">
                                                    <img src="img/products/prod-11-1-70x81.jpg" alt="Product Thumnail">
                                                </td>
                                                <td class="product-name text-start wide-column">
                                                    <h3>
                                                        <a href="product-details.html">Dress with bffelt</a>
                                                    </h3>
                                                </td>
                                                <td class="product-price">
                                                    <span class="product-price-wrapper">
                                                        <span class="money">$49.00</span>
                                                    </span>
                                                </td>
                                                <td class="product-quantity">
                                                    <div class="quantity">
                                                        <input type="number" class="quantity-input" name="qty" id="qty-4" value="1" min="1">
                                                    </div>
                                                </td>                                               
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div class="cart-collaterals">
                        <div class="cart-totals">
                            <h4 class="mb--15"><b> RESUMEN DE PEDIDO </b> </h4>
                            <div class="table-content table-responsive">
                                <table class="table order-table">
                                    <tbody>
                                        <tr>
                                            <th>Super skinny blazer</th>
                                            <td style="text-align: right;">S/ 1,196.00</td>
                                        </tr>
                                        <tr>
                                            <th>Super skinny blazer</th>
                                            <td style="text-align: right;">S/ 196.00</td>
                                        </tr>
                                        <tr class="order-total">
                                            <th>Total</th>
                                            <td>
                                                <span class="product-price-wrapper">
                                                    <span class="money">S/ 1,392.00</span>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <a href="checkout.php" class="btn btn-fullwidth btn-style-1">
                            Continuar con la compra
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'vistas/footer.php'; ?>
<?php include 'vistas/scripts.php'; ?>