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
    $tablaTallasArchivo = trim((string) ($producto['tabla_tallas'] ?? ''));
    $tablaTallasUrl = '';

    if ($tablaTallasArchivo !== '') {
        $limpiaTabla = ltrim($tablaTallasArchivo, '/');
        if (strpos($limpiaTabla, 'uploads/productos/') === 0) {
            $tablaTallasUrl = asset_url($limpiaTabla);
        } elseif (strpos($limpiaTabla, 'uploads/') === 0) {
            $tablaTallasUrl = asset_url($limpiaTabla);
        } else {
            $tablaTallasUrl = asset_url('uploads/productos/' . $limpiaTabla);
        }
    }
?>
<style>
.grid-imagenes{display:flex;flex-wrap:wrap;gap:12px}
.grid-imagenes .item{position:relative;width:200px;height:200px;border:1px solid #eee;border-radius:8px;overflow:hidden;cursor:pointer;background:#fafafa}
.grid-imagenes .item img{width:100%;height:100%;object-fit:cover}
.grid-imagenes .item .btn-del{position:absolute;top:6px;right:6px;background:#fff;border:1px solid #ccc;border-radius:50%;width:28px;height:28px;line-height:26px;text-align:center;cursor:pointer;color:#333;padding:0;font-weight:bold}
.grid-imagenes .item .tag-principal{position:absolute;bottom:6px;left:6px;background:#111;color:#fff;padding:2px 6px;border-radius:4px;font-size:12px;display:none}
.grid-imagenes .item .tag-principal.activo{display:inline-block}
.grid-imagenes .item .btn-del:hover{background:#f8f8f8}
.tabla-tallas-preview{position:relative;display:inline-block;margin-top:12px}
.tabla-tallas-preview img{width:200px;max-width:100%;height:auto;border-radius:8px;border:1px solid #eee;object-fit:cover}
.tabla-tallas-preview .btn-del-tabla{position:absolute;top:6px;right:6px;background:#e74c3c;color:#fff;border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;font-size:16px;line-height:24px;padding:0}
.tabla-tallas-preview .btn-del-tabla:hover{opacity:0.85}
</style>
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
                        <input type="number" name="stock" id="stock" min="0" class="form-control <?= isset($errores['stock']) ? 'is-invalid' : ''; ?>" value="<?= e(old('stock', (string) ($producto['stock'] ?? '0'))); ?>">
                        <?php if (isset($errores['stock'])): ?>
                            <div class="invalid-feedback"><?= e($errores['stock']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" id="sku" class="form-control" value="<?= e(old('sku', $producto['sku'] ?? '')); ?>">
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Visibilidad</label>
                        <div class="form-check form-switch">
                            <?php
                                $visiblePredeterminado = (int) ($producto['visible'] ?? ($producto['estado'] ?? 1));
                                $visibleOld = old('visible', $visiblePredeterminado ? '1' : '0');
                                $visibleMarcado = $visibleOld === '1' || $visibleOld === 'on';
                            ?>
                            <input class="form-check-input" type="checkbox" role="switch" id="visible" name="visible" value="1" <?= $visibleMarcado ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="visible">Producto visible en la tienda</label>
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
                        <input type="file" name="tabla_tallas" id="tabla_tallas" accept=".jpg,.jpeg,.png,.webp,image/*" class="form-control">
                        <small class="form-text d-block mt-1">Formatos permitidos: JPG, PNG o WEBP.</small>
                        <?php if ($tablaTallasUrl !== ''): ?>
                            <div class="tabla-tallas-preview" data-has-tabla="1">
                                <img src="<?= e($tablaTallasUrl); ?>" alt="Tabla de tallas actual">
                                <?php if ($esEdicion && !empty($producto['id'])): ?>
                                    <button type="button" class="btn-del-tabla" data-url="<?= e(base_url('admin/productos/eliminar_tabla_tallas/' . (int) $producto['id'])); ?>" title="Eliminar tabla de tallas">&times;</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="imagenes" class="form-label">Imágenes del producto</label>
                        <input type="file" name="imagenes[]" id="imagenes" accept="image/*" multiple class="form-control">
                        <small class="form-text d-block mt-1">JPG/PNG/WEBP. Tamaño recomendado 1000×1000. Se generará miniatura.</small>
                        <input type="hidden" name="imagen_principal_nueva" id="imagen_principal_nueva" value="<?= e(old('imagen_principal_nueva', '')); ?>">
                        <div id="grid-imagenes" class="grid-imagenes mt-3"></div>
                        <small class="form-text text-muted">Haz clic en una imagen para marcarla como principal. Usa la ✕ para quitarla antes de guardar.</small>
                        <?php if (!empty($producto['imagenes']) && is_array($producto['imagenes'])): ?>
                            <div id="grid-imagenes-existentes" class="grid-imagenes mt-3">
                                <?php foreach ($producto['imagenes'] as $imagen): ?>
                                    <?php
                                        $ruta = trim((string) ($imagen['ruta'] ?? ''));
                                        if ($ruta === '') {
                                            continue;
                                        }
                                        $limpia = ltrim($ruta, '/');
                                        if (strpos($limpia, 'uploads/products/') === 0 || strpos($limpia, 'uploads/productos/') === 0) {
                                            $rutaPublica = asset_url($limpia);
                                        } elseif (strpos($limpia, 'products/') === 0 || strpos($limpia, 'productos/') === 0) {
                                            $rutaPublica = asset_url('uploads/' . $limpia);
                                        } else {
                                            $rutaPublica = asset_url('uploads/productos/' . $limpia);
                                        }
                                        $esPrincipal = (int) ($imagen['es_principal'] ?? 0) === 1;
                                        $imagenId = (int) ($imagen['id'] ?? 0);
                                        $deleteUrl = ($esEdicion && !empty($producto['id']) && $imagenId > 0)
                                            ? base_url('admin/productos/eliminar_imagen/' . $imagenId)
                                            : '';
                                    ?>
                                    <div class="item" data-imagen-id="<?= (int) $imagenId; ?>">
                                        <img src="<?= e($rutaPublica); ?>" alt="<?= e($ruta); ?>">
                                        <?php if ($deleteUrl !== ''): ?>
                                            <button type="button" class="btn-del btn-del-existente" data-url="<?= e($deleteUrl); ?>">&times;</button>
                                        <?php endif; ?>
                                        <span class="tag-principal<?= $esPrincipal ? ' activo' : ''; ?>">Principal</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('imagenes');
    const gridNuevas = document.getElementById('grid-imagenes');
    const gridExistentes = document.getElementById('grid-imagenes-existentes');
    const inputPrincipal = document.getElementById('imagen_principal_nueva');
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    const tablaTallasPreview = document.querySelector('.tabla-tallas-preview');
    const csrfToken = csrfInput ? csrfInput.value : '';

    if (typeof DataTransfer === 'undefined') {
        return;
    }

    if (!input || !gridNuevas || !inputPrincipal) {
        return;
    }

    if (tablaTallasPreview) {
        const btnTabla = tablaTallasPreview.querySelector('.btn-del-tabla');
        if (btnTabla) {
            btnTabla.addEventListener('click', async (event) => {
                event.preventDefault();
                if (!window.confirm('¿Eliminar la tabla de tallas?')) {
                    return;
                }

                const url = btnTabla.dataset.url || '';
                if (url === '') {
                    return;
                }

                try {
                    const respuesta = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            csrf_token: csrfToken,
                        }),
                    });

                    if (!respuesta.ok) {
                        throw new Error('No se pudo eliminar la tabla de tallas.');
                    }

                    const data = await respuesta.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Ocurrió un error al eliminar la tabla de tallas.');
                    }

                    tablaTallasPreview.remove();
                } catch (error) {
                    alert(error.message || 'Ocurrió un error al eliminar la tabla de tallas.');
                }
            });
        }
    }

    let dataTransfer = new DataTransfer();
    let principalIndex = null;
    let tienePrincipalExistente = gridExistentes ? gridExistentes.querySelector('.tag-principal.activo') !== null : false;

    const valorInicialPrincipal = inputPrincipal.value.trim();
    if (valorInicialPrincipal !== '') {
        const indice = parseInt(valorInicialPrincipal, 10);
        if (!Number.isNaN(indice)) {
            principalIndex = indice;
        }
    }

    const sincronizarInput = () => {
        input.files = dataTransfer.files;
    };

    const actualizarPrincipalHidden = () => {
        if (principalIndex === null || dataTransfer.files.length === 0) {
            inputPrincipal.value = '';
        } else {
            inputPrincipal.value = String(principalIndex);
        }
    };

    const renderPreviews = () => {
        const archivos = Array.from(dataTransfer.files);

        if (archivos.length === 0) {
            gridNuevas.innerHTML = '';
            principalIndex = null;
            actualizarPrincipalHidden();
            return;
        }

        if (principalIndex === null) {
            if (!tienePrincipalExistente) {
                principalIndex = 0;
            }
        } else if (principalIndex >= archivos.length) {
            principalIndex = archivos.length - 1;
        }

        if (principalIndex !== null && principalIndex < 0) {
            principalIndex = 0;
        }

        gridNuevas.innerHTML = '';

        archivos.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'item';
            item.dataset.index = String(index);

            const img = document.createElement('img');
            const reader = new FileReader();
            reader.onload = function (event) {
                if (event && event.target && event.target.result) {
                    img.src = event.target.result;
                }
            };
            reader.readAsDataURL(file);
            item.appendChild(img);

            const btnDel = document.createElement('button');
            btnDel.type = 'button';
            btnDel.className = 'btn-del';
            btnDel.innerHTML = '&times;';
            btnDel.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                eliminarImagenNueva(index);
            });
            item.appendChild(btnDel);

            const tagPrincipal = document.createElement('span');
            tagPrincipal.className = 'tag-principal';
            tagPrincipal.textContent = 'Principal';
            if (principalIndex === index) {
                tagPrincipal.classList.add('activo');
            }
            item.appendChild(tagPrincipal);

            item.addEventListener('click', (event) => {
                if (event.target instanceof HTMLElement && event.target.classList.contains('btn-del')) {
                    return;
                }
                marcarPrincipal(index);
            });

            gridNuevas.appendChild(item);
        });

        actualizarPrincipalHidden();
    };

    const marcarPrincipal = (index) => {
        if (index < 0 || index >= dataTransfer.files.length) {
            return;
        }
        principalIndex = index;
        tienePrincipalExistente = false;
        renderPreviews();
    };

    const eliminarImagenNueva = (index) => {
        const archivos = Array.from(dataTransfer.files);
        if (index < 0 || index >= archivos.length) {
            return;
        }

        archivos.splice(index, 1);
        dataTransfer = new DataTransfer();
        archivos.forEach((file) => dataTransfer.items.add(file));

        if (principalIndex !== null) {
            if (archivos.length === 0) {
                principalIndex = null;
            } else if (index === principalIndex) {
                principalIndex = 0;
            } else if (index < principalIndex) {
                principalIndex -= 1;
            }
        }

        sincronizarInput();
        renderPreviews();
    };

    input.addEventListener('change', (event) => {
        const archivosNuevos = Array.from(event.target.files || []);
        if (archivosNuevos.length === 0) {
            return;
        }

        const existentes = Array.from(dataTransfer.files);
        dataTransfer = new DataTransfer();
        existentes.forEach((file) => dataTransfer.items.add(file));
        archivosNuevos.forEach((file) => dataTransfer.items.add(file));

        sincronizarInput();

        if (principalIndex === null && !tienePrincipalExistente) {
            principalIndex = 0;
        }

        renderPreviews();
        actualizarPrincipalHidden();
        input.value = '';
    });

    const marcarPrincipalExistente = (nuevoId) => {
        if (!gridExistentes) {
            return;
        }

        const items = gridExistentes.querySelectorAll('.item');
        items.forEach((item) => {
            const badge = item.querySelector('.tag-principal');
            if (!(badge instanceof HTMLElement)) {
                return;
            }

            if (nuevoId !== null && parseInt(item.dataset.imagenId || '0', 10) === nuevoId) {
                badge.classList.add('activo');
            } else if (nuevoId === null) {
                badge.classList.remove('activo');
            } else {
                badge.classList.remove('activo');
            }
        });

        if (nuevoId !== null) {
            tienePrincipalExistente = true;
        } else {
            tienePrincipalExistente = gridExistentes.querySelector('.tag-principal.activo') !== null;
        }

        if (!tienePrincipalExistente && principalIndex === null && dataTransfer.files.length > 0) {
            renderPreviews();
        }
    };

    if (gridExistentes) {
        gridExistentes.addEventListener('click', async (event) => {
            const boton = event.target instanceof HTMLElement ? event.target.closest('.btn-del-existente') : null;
            if (!boton) {
                return;
            }

            event.preventDefault();
            const url = boton.dataset.url || '';
            if (url === '') {
                return;
            }

            const item = boton.closest('.item');
            if (!item) {
                return;
            }

            const confirmar = window.confirm('¿Eliminar esta imagen?');
            if (!confirmar) {
                return;
            }

            const badgePrincipal = item.querySelector('.tag-principal');
            const eraPrincipal = badgePrincipal ? badgePrincipal.classList.contains('activo') : false;

            try {
                const respuesta = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        csrf_token: csrfToken,
                    }),
                });

                if (!respuesta.ok) {
                    throw new Error('No se pudo eliminar la imagen.');
                }

                const data = await respuesta.json();
                if (!data.success) {
                    throw new Error(data.message || 'Ocurrió un error al eliminar la imagen.');
                }

                item.remove();

                if (eraPrincipal || typeof data.nuevoPrincipalId !== 'undefined') {
                    const valorNuevo = data.nuevoPrincipalId;
                    const nuevoId = valorNuevo !== null && valorNuevo !== undefined
                        ? parseInt(String(valorNuevo), 10)
                        : null;
                    if (!isNaN(nuevoId) && nuevoId !== null) {
                        marcarPrincipalExistente(nuevoId);
                    } else if (eraPrincipal) {
                        marcarPrincipalExistente(null);
                    }
                }
            } catch (error) {
                alert(error.message || 'Ocurrió un error al eliminar la imagen.');
            }
        });
    }
});
</script>
