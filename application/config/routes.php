<?php

return [
    '' => ['HomeController', 'index'],
    'buscar' => ['BuscarController', 'index'],
    'search/ajax' => ['SearchController', 'ajax'],
    'productos' => ['ProductosController', 'index'],
    'productos/detalle' => ['ProductosController', 'detalle'],
    'detalle_producto' => ['ProductosController', 'detalle'],
    'ofertas' => ['ProductosController', 'ofertas'],
    'novedades' => ['ProductosController', 'novedades'],
    'populares' => ['ProductosController', 'populares'],
    'por_mayor' => ['ProductosController', 'porMayor'],
    'categoria' => ['CategoriaController', 'index'],
    'categoria/(:segment)' => ['CategoriaController', 'ver'],
    'checkout' => ['CheckoutController', 'index'],
    'checkout/procesar' => ['CheckoutController', 'procesar'],
    'checkout/obtener_datos_cliente' => ['CheckoutController', 'obtener_datos_cliente'],
    'carrito' => ['CarritoController', 'index'],
    'carrito/agregar' => ['CarritoController', 'agregar'],
    'carrito/eliminar' => ['CarritoController', 'eliminar'],
    'carrito/actualizar' => ['CarritoController', 'actualizar'],
    'carrito/actualizar_ajax' => ['CarritoController', 'actualizar_ajax'],
    'carrito/sync_ajax' => ['CarritoController', 'sync_ajax'],
    'carrito/vaciar' => ['CarritoController', 'vaciar'],
    'carrito_compras' => ['CarritoController', 'index'],
    'blog' => ['BlogController', 'index'],
    'blog/ver' => ['BlogController', 'ver'],
    'ver_blog' => ['BlogController', 'ver'],
    'mi-cuenta' => ['MiCuentaController', 'index'],
    'mi_cuenta' => ['MiCuentaController', 'index'],
    'mi_cuenta/eliminar' => ['MiCuentaController', 'eliminar'],
    'ver-orden' => ['CheckoutController', 'ver_orden'],
    'ver_orden' => ['CheckoutController', 'ver_orden'],

    // =======================
    // Para el Cliente (FRONT)
    // =======================
    'para-el-cliente/{slug}' => ['ClienteController', 'index'],
    'para-el-cliente'        => ['ClienteController', 'index'],

    'para_el_cliente/{slug}' => ['ClienteController', 'index'],
    'para_el_cliente'        => ['ClienteController', 'index'],


    'libro-de-reclamaciones' => ['ClienteController', 'libro'],
    'libro_de_reclamaciones' => ['ClienteController', 'libro'],
    'admin' => ['AdminController', 'login'],
    'admin/login' => ['AdminController', 'login'],
    'admin/dashboard' => ['AdminController', 'dashboard'],
    'admin/productos' => ['AdminController', 'productos'],
];
