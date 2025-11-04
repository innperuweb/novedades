<?php
    $formAction = $esEdicion ? base_url('admin/productos/actualizar/' . (int) ($producto['id'] ?? 0)) : base_url('admin/productos/guardar');
    $coloresTexto = '';
    if (!empty($producto['colores'])) {
        $coloresTexto = is_array($producto['colores']) ? implode(', ', $producto['colores']) : (string) $producto['colores'];
    }
    $tallasTexto = '';
    if (!empty($producto['tallas'])) {
        $tallasTexto = is_array($producto['tallas']) ? implode(', ', $producto['tallas']) : (string) $producto['tallas'];
    }
    $seleccionadas = $producto['subcategorias'] ?? [];
    $seleccionadas = is_array($seleccionadas) ? array_map('intval', $seleccionadas) : [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0"><?= $esEdicion ? 'Editar producto' : 'Nuevo producto'; ?></h1>
    <a class="btn btn-outline-secondary" href="<?= base_url('admin/productos'); ?>">Volver</a>
</div>

<form action="<?= $formAction; ?>" method="post" class="row g-4" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : ''; ?>" value="<?= e($producto['nombre'] ?? ''); ?>" required>
                    <?php if (isset($errores['nombre'])): ?>
                        <div class="invalid-feedback"><?= e($errores['nombre']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" name="marca" id="marca" class="form-control" placeholder="Ejemplo: Nike" value="<?= e($producto['marca'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control"><?= e($producto['descripcion'] ?? ''); ?></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" name="precio" id="precio" min="0" step="0.01" class="form-control <?= isset($errores['precio']) ? 'is-invalid' : ''; ?>" value="<?= e($producto['precio'] ?? '0'); ?>" required>
                        <?php if (isset($errores['precio'])): ?>
                            <div class="invalid-feedback"><?= e($errores['precio']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" id="stock" min="0" class="form-control <?= isset($errores['stock']) ? 'is-invalid' : ''; ?>" value="<?= e($producto['stock'] ?? '0'); ?>">
                        <?php if (isset($errores['stock'])): ?>
                            <div class="invalid-feedback"><?= e($errores['stock']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" id="sku" class="form-control" value="<?= e($producto['sku'] ?? ''); ?>">
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Estado</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" <?= (int) ($producto['activo'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">Producto visible en la tienda</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Variantes</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="colores" class="form-label">Colores (separados por coma)</label>
                        <input type="text" name="colores" id="colores" class="form-control" value="<?= e($coloresTexto); ?>" placeholder="Rojo, Azul, Negro">
                    </div>
                    <div class="col-md-6">
                        <label for="tallas" class="form-label">Tallas (separadas por coma)</label>
                        <input type="text" name="tallas" id="tallas" class="form-control" value="<?= e($tallasTexto); ?>" placeholder="S, M, L">
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Recursos multimedia</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tabla_tallas" class="form-label">Tabla de Tallas</label>
                        <input type="file" name="tabla_tallas" id="tabla_tallas" accept="image/*" class="form-control">
                        <?php if (!empty($producto['tabla_tallas'])): ?>
                            <div class="form-text">Archivo actual: <?= e($producto['tabla_tallas']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="imagenes" class="form-label">Imágenes del producto</label>
                        <input type="file" name="imagenes[]" id="imagenes" accept="image/*" multiple class="form-control">
                        <div class="form-text">Puedes seleccionar múltiples imágenes.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Categorías</h2>
                <select name="subcategorias[]" class="form-select" multiple size="10">
                    <?php
                        $grupoActual = null;
                        foreach ($subcategorias as $subcategoria) {
                            $categoriaNombre = $subcategoria['categoria_nombre'] ?? 'Sin categoría';
                            if ($grupoActual !== $categoriaNombre) {
                                if ($grupoActual !== null) {
                                    echo '</optgroup>';
                                }
                                $grupoActual = $categoriaNombre;
                                echo '<optgroup label="' . e($categoriaNombre) . '">';
                            }
                            $idSub = (int) ($subcategoria['id'] ?? 0);
                            $seleccionado = in_array($idSub, $seleccionadas, true) ? 'selected' : '';
                            echo '<option value="' . $idSub . '" ' . $seleccionado . '>' . e($subcategoria['nombre'] ?? '') . '</option>';
                        }
                        if ($grupoActual !== null) {
                            echo '</optgroup>';
                        }
                    ?>
                </select>
                <div class="form-hint">Mantén presionada la tecla Ctrl o Cmd para seleccionar múltiples opciones.</div>
            </div>
        </div>
        <div class="d-grid gap-2 mt-3">
            <button type="submit" class="btn btn-primary btn-lg"><?= $esEdicion ? 'Guardar cambios' : 'Crear producto'; ?></button>
        </div>
    </div>
</form>
