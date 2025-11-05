<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';
require_once APP_PATH . '/models/SubcategoriaModel.php';
require_once APP_PATH . '/helpers/security_helper.php';

class ProductosController extends BaseController
{
    public function index(): void
    {
        $slugSubcategoria = isset($_GET['subcat']) ? sanitize_string((string) $_GET['subcat']) : '';
        $slugSubcategoria = trim($slugSubcategoria);

        $orden = isset($_GET['order']) ? sanitize_string((string) $_GET['order']) : '';
        $ordenesPermitidos = ['precio_asc', 'precio_desc', 'nombre_asc', 'nombre_desc'];
        if (!in_array($orden, $ordenesPermitidos, true)) {
            $orden = '';
        }

        $paginaActual = isset($_GET['page']) ? sanitize_int($_GET['page']) : 1;
        $paginaActual = $paginaActual !== null && $paginaActual > 0 ? $paginaActual : 1;
        $limite = 20;

        $productos = [];
        $categoriaId = null;
        $subcategorias = [];

        $minPrecio = $this->sanitizarPrecio($_POST['min_precio'] ?? null, 0.0);
        $maxPrecio = $this->sanitizarPrecio($_POST['max_precio'] ?? null, 10000.0);

        $esSolicitudPost = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST';
        $aplicarFiltroPrecio = $esSolicitudPost && ($slugSubcategoria !== '');

        if ($slugSubcategoria !== '') {
            $subcategoria = SubcategoriaModel::obtenerPorSlug($slugSubcategoria);

            if ($subcategoria !== null) {
                $categoriaId = (int) ($subcategoria['categoria_id'] ?? 0);
                if ($categoriaId > 0) {
                    $subcategorias = SubcategoriaModel::obtenerPorCategoria($categoriaId);
                }

                if ($aplicarFiltroPrecio) {
                    if ($minPrecio > $maxPrecio) {
                        [$minPrecio, $maxPrecio] = [$maxPrecio, $minPrecio];
                    }

                    $productos = ProductoModel::filtrarPorPrecio($slugSubcategoria, $minPrecio, $maxPrecio);
                } elseif ($orden !== '') {
                    $productos = ProductoModel::obtenerFiltrados($slugSubcategoria, $orden);
                } else {
                    $productos = ProductoModel::obtenerPorSubcategoria($slugSubcategoria);
                }
            }
        }

        $this->render('productos', [
            'productos' => $productos,
            'slug_subcat' => $slugSubcategoria,
            'orden' => $orden,
            'pagina_actual' => $paginaActual,
            'limite' => $limite,
            'categoria_id' => $categoriaId,
            'subcategorias' => $subcategorias,
            'min_precio' => $minPrecio,
            'max_precio' => $maxPrecio,
        ]);
    }

    public function detalle(): void
    {
        $id = isset($_GET['id']) ? sanitize_int($_GET['id']) : null;
        $id = $id ?? 1;

        $model = new ProductoModel();
        $producto = $model->getById($id);

        if ($producto === null) {
            $producto = $model->getById(1);
            if ($producto === null) {
                $this->render('detalle_producto', ['producto' => null]);
                return;
            }
        }

        $producto['colores'] = $this->normalizarOpciones($producto['colores'] ?? []);
        $producto['tallas'] = $this->normalizarOpciones($producto['tallas'] ?? []);
        if (!isset($producto['imagenes']) || !is_array($producto['imagenes'])) {
            $producto['imagenes'] = $model->obtenerImagenesPorProducto((int) ($producto['id'] ?? 0));
        }

        $imagenes = $producto['imagenes'] ?? [];

        $this->render('detalle_producto', compact('producto', 'imagenes'));
    }

    public function ofertas(): void
    {
        $this->render('ofertas');
    }

    private function sanitizarPrecio($valor, float $predeterminado): float
    {
        if ($valor === null || $valor === '') {
            return $predeterminado;
        }

        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $valor = (string) $valor;
        $valor = str_replace(',', '.', $valor);
        $valor = preg_replace('/[^0-9.\-]/', '', $valor);

        if ($valor === '' || !is_numeric($valor)) {
            return $predeterminado;
        }

        return (float) $valor;
    }

    private function normalizarOpciones($valor): array
    {
        if (is_string($valor)) {
            $decoded = json_decode($valor, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $valor = $decoded;
            } else {
                $valor = preg_split('/[;,]+/', $valor) ?: [];
            }
        }

        if (!is_array($valor)) {
            return [];
        }

        $items = array_map(static fn ($item): string => trim((string) $item), $valor);
        $items = array_filter($items, static fn ($item): bool => $item !== '');

        return array_values(array_unique($items));
    }
}
