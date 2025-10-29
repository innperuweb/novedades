<?php
require_once __DIR__ . '/../config/constants.php';
include_once __DIR__ . '/vistas/head.php';
include_once __DIR__ . '/vistas/header.php';
?>
<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Monday to Sunday</h1>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper">
    <div class="page-content-inner">
        <div class="container">
            <div class="row justify-content-center pt--75 pb--80 pt-md--55 pb-md--60 pt-sm--35 pb-sm--40">
                <div class="col-lg-6">

                    <div class="post-media">
                        <div class="image">
                            <img src="<?php echo ASSET_PATH; ?>img/blog/image.jpg" alt="Blog">
                            <a href="ver_blog.php" class="link-overlay"></a>
                        </div>
                    </div>
                    <div class="post-meta">
                        <a href="blog.html" class="posted-on" tabindex="0">September 16, 2018</a>
                    </div>

                    <p class="heading-color mb--30">To track your order please enter your Order ID
                        in the box below and press the "Track" button. This was given to you on your receipt and
                        in the confirmation email you should have received. <br>
                        To track your order please enter your Order ID
                        in the box below and press the "Track" button. This was given to you on your receipt and
                        in the confirmation email you should have received. <br>
                        To track your order please enter your Order ID
                        in the box below and press the "Track" button. This was given to you on your receipt and
                        in the confirmation email you should have received. <br>
                        To track your order please enter your Order ID
                        in the box below and press the "Track" button. This was given to you on your receipt and
                        in the confirmation email you should have received.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/vistas/footer.php'; ?>
<?php include_once __DIR__ . '/vistas/scripts.php'; ?>
