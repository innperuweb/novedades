<?php

return [
    '' => ['HomeController', 'index'],
    'productos' => ['ProductosController', 'index'],
    'productos/detalle' => ['ProductosController', 'detalle'],
    'detalle_producto' => ['ProductosController', 'detalle'],
    'ofertas' => ['ProductosController', 'ofertas'],
    'checkout' => ['CheckoutController', 'index'],
    'carrito' => ['CheckoutController', 'carrito'],
    'carrito_compras' => ['CheckoutController', 'carrito'],
    'blog' => ['BlogController', 'index'],
    'blog/ver' => ['BlogController', 'ver'],
    'ver_blog' => ['BlogController', 'ver'],
    'mi-cuenta' => ['CuentaController', 'index'],
    'mi_cuenta' => ['CuentaController', 'index'],
    'ver-orden' => ['CuentaController', 'verOrden'],
    'ver_orden' => ['CuentaController', 'verOrden'],
    'para-el-cliente' => ['ClienteController', 'index'],
    'para_el_cliente' => ['ClienteController', 'index'],
    'libro-de-reclamaciones' => ['ClienteController', 'libro'],
    'libro_de_reclamaciones' => ['ClienteController', 'libro'],
    'admin' => ['AdminController', 'index'],
];
