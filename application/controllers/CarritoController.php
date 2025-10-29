<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';
require_once APP_PATH . '/helpers/session_helper.php';
require_once APP_PATH . '/helpers/security_helper.php';

class CarritoController extends BaseController
{
    public function index(): void
    {
        $carrito = get_cart_session();

        $this->render('carrito_compras', compact('carrito'));
    }

    public function agregar(): void
    {
        if (!isset($_POST['id'])) {
            $this->redirectToCarrito();
        }

        $id = sanitize_int($_POST['id']);
        $cantidad = isset($_POST['cantidad']) ? sanitize_int($_POST['cantidad']) : 1;

        if ($id === null) {
            $this->redirectToCarrito();
        }

        $cantidad = $cantidad !== null ? max(1, $cantidad) : 1;

        $model = new ProductoModel();
        $producto = $model->getById($id);

        if ($producto !== null) {
            $item = [
                'id' => (int) $producto['id'],
                'nombre' => (string) $producto['nombre'],
                'precio' => (float) $producto['precio'],
                'imagen' => (string) ($producto['imagen'] ?? ''),
                'cantidad' => $cantidad,
            ];

            $carrito = get_cart_session();
            $encontrado = false;

            foreach ($carrito as &$productoCarrito) {
                if ((int) $productoCarrito['id'] === $item['id']) {
                    $productoCarrito['cantidad'] += $item['cantidad'];
                    $encontrado = true;
                    break;
                }
            }
            unset($productoCarrito);

            if (!$encontrado) {
                $carrito[] = $item;
            }

            set_cart_session($carrito);
        }

        $this->redirectToCarrito();
    }

    public function eliminar(): void
    {
        if (!isset($_GET['id'])) {
            $this->redirectToCarrito();
        }

        $id = sanitize_int($_GET['id']);

        if ($id !== null) {
            $carrito = get_cart_session();

            foreach ($carrito as $key => $item) {
                if ((int) ($item['id'] ?? 0) === $id) {
                    unset($carrito[$key]);
                }
            }

            $carrito = array_values($carrito);

            if ($carrito === []) {
                clear_cart_session();
            } else {
                set_cart_session($carrito);
            }
        }

        $this->redirectToCarrito();
    }

    public function vaciar(): void
    {
        clear_cart_session();

        $this->redirectToCarrito();
    }

    public function actualizar(): void
    {
        if (!isset($_POST['id'], $_POST['cantidad'])) {
            $this->redirectToCarrito();
        }

        $id = sanitize_int($_POST['id']);
        $cantidad = sanitize_int($_POST['cantidad']);

        if ($id !== null && $cantidad !== null && $cantidad > 0) {
            $carrito = get_cart_session();

            foreach ($carrito as &$item) {
                if ((int) ($item['id'] ?? 0) === $id) {
                    $item['cantidad'] = $cantidad;
                    break;
                }
            }
            unset($item);

            set_cart_session($carrito);
        }

        $this->redirectToCarrito();
    }

    private function redirectToCarrito(): void
    {
        header('Location: ' . base_url('carrito'));
        exit;
    }
}
