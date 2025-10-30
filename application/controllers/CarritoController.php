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
        $color = sanitize_string($_POST['color'] ?? '');
        $talla = sanitize_string($_POST['talla'] ?? '');

        if ($id === null) {
            $this->redirectToCarrito();
        }

        $cantidad = $cantidad !== null ? max(1, $cantidad) : 1;

        $model = new ProductoModel();
        $producto = $model->getById($id);

        if ($producto !== null) {
            $coloresDisponibles = array_map('strval', $producto['colores'] ?? []);
            $tallasDisponibles = array_map('strval', $producto['tallas'] ?? []);

            if ($coloresDisponibles !== [] && !in_array($color, $coloresDisponibles, true)) {
                $color = '';
            }

            if ($tallasDisponibles !== [] && !in_array($talla, $tallasDisponibles, true)) {
                $talla = '';
            }

            $uid = $this->generateItemUid($id, $color, $talla);

            $item = [
                'id' => (int) $producto['id'],
                'nombre' => (string) $producto['nombre'],
                'precio' => (float) $producto['precio'],
                'imagen' => (string) ($producto['imagen'] ?? ''),
                'cantidad' => $cantidad,
                'color' => $color,
                'talla' => $talla,
                'uid' => $uid,
            ];

            $carrito = $this->normalizeCarrito(get_cart_session());
            $encontrado = false;

            foreach ($carrito as &$productoCarrito) {
                if (($productoCarrito['uid'] ?? '') === $uid) {
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
        $uid = isset($_GET['uid']) ? sanitize_string($_GET['uid']) : '';
        $id = isset($_GET['id']) ? sanitize_int($_GET['id']) : null;

        if ($uid === '' && $id === null) {
            $this->redirectToCarrito();
        }

        $carrito = $this->normalizeCarrito(get_cart_session());

        foreach ($carrito as $key => $item) {
            $itemUid = (string) ($item['uid'] ?? '');
            $itemId = (int) ($item['id'] ?? 0);

            if (($uid !== '' && hash_equals($itemUid, $uid)) || ($uid === '' && $id !== null && $itemId === $id)) {
                unset($carrito[$key]);
            }
        }

        $carrito = array_values($carrito);

        if ($carrito === []) {
            clear_cart_session();
        } else {
            set_cart_session($carrito);
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
        if (!isset($_POST['cantidad'])) {
            $this->redirectToCarrito();
        }

        $uid = isset($_POST['uid']) ? sanitize_string($_POST['uid']) : '';
        $id = isset($_POST['id']) ? sanitize_int($_POST['id']) : null;
        $cantidad = sanitize_int($_POST['cantidad']);

        if ($cantidad !== null && $cantidad > 0 && ($uid !== '' || $id !== null)) {
            $carrito = $this->normalizeCarrito(get_cart_session());

            foreach ($carrito as &$item) {
                $itemUid = (string) ($item['uid'] ?? '');
                $itemId = (int) ($item['id'] ?? 0);

                if (($uid !== '' && hash_equals($itemUid, $uid)) || ($uid === '' && $id !== null && $itemId === $id)) {
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

    private function generateItemUid(int $id, string $color, string $talla): string
    {
        $normalizar = static function (string $valor): string {
            $valor = trim($valor);
            if ($valor === '') {
                return '';
            }

            if (function_exists('mb_strtolower')) {
                $valor = mb_strtolower($valor, 'UTF-8');
            } else {
                $valor = strtolower($valor);
            }

            return $valor;
        };

        $colorNormalizado = $normalizar($color);
        $tallaNormalizada = $normalizar($talla);

        return sha1($id . '|' . $colorNormalizado . '|' . $tallaNormalizada);
    }

    private function normalizeCarrito(array $items): array
    {
        foreach ($items as &$item) {
            $id = sanitize_int($item['id'] ?? null) ?? 0;
            $color = sanitize_string($item['color'] ?? '');
            $talla = sanitize_string($item['talla'] ?? '');

            if (empty($item['uid'])) {
                $item['uid'] = $this->generateItemUid((int) $id, $color, $talla);
            }

            $item['id'] = (int) $id;
            $item['color'] = $color;
            $item['talla'] = $talla;
            $item['cantidad'] = max(1, (int) ($item['cantidad'] ?? 1));
        }
        unset($item);

        return $items;
    }
}
