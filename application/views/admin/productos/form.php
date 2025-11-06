<?php
    $formAction = $esEdicion ? base_url('admin/productos/actualizar/' . (int) ($producto['id'] ?? 0)) : base_url('admin/productos/guardar');
    $productoId = (int) ($producto['id'] ?? 0);
    $coloresTexto = '';
    $coloresOld = old('colores', null);
    if ($coloresOld !== null) {
        if (is_array($coloresOld)) {
            $coloresOld = array_map(static fn ($item): string => trim((string) $item), $coloresOld);
            $coloresOld = array_filter($coloresOld, static fn ($item): bool => $item !== '');
            $coloresTexto = implode(', ', $coloresOld);
        } else {
            $coloresTexto = trim((string) $coloresOld);
        }
    } elseif (!empty($producto['colores'])) {
        $coloresTexto = is_array($producto['colores']) ? implode(', ', $producto['colores']) : (string) $producto['colores'];
    }
    $tallasTexto = '';
    $tallasOld = old('tallas', null);
    if ($tallasOld !== null) {
        if (is_array($tallasOld)) {
            $tallasOld = array_map(static fn ($item): string => trim((string) $item), $tallasOld);
            $tallasOld = array_filter($tallasOld, static fn ($item): bool => $item !== '');
            $tallasTexto = implode(', ', $tallasOld);
        } else {
            $tallasTexto = trim((string) $tallasOld);
        }
    } elseif (!empty($producto['tallas'])) {
        $tallasTexto = is_array($producto['tallas']) ? implode(', ', $producto['tallas']) : (string) $producto['tallas'];
    }
    $seleccionadas = $producto['subcategorias'] ?? [];
    $seleccionadas = is_array($seleccionadas) ? array_map('intval', $seleccionadas) : [];
    $tablaTallasArchivo = trim((string) ($producto['tabla_tallas'] ?? ''));
    $tablaTallasUrl = '';
    if ($tablaTallasArchivo !== '') {
        $limpiaTabla = ltrim($tablaTallasArchivo, '/');

        if (strpos($limpiaTabla, 'assets/') === 0) {
            $limpiaTabla = ltrim(substr($limpiaTabla, strlen('assets/')) ?: '', '/');
        }

        if (strpos($limpiaTabla, 'uploads/') === 0) {
            $tablaTallasUrl = asset_url($limpiaTabla);
        } elseif (strpos($limpiaTabla, 'tabla_tallas/') === 0) {
            $tablaTallasUrl = asset_url('uploads/' . $limpiaTabla);
        } elseif (strpos($limpiaTabla, 'uploads/productos/') === 0) {
            $tablaTallasUrl = asset_url($limpiaTabla);
        } elseif (strpos($limpiaTabla, 'productos/') === 0 || strpos($limpiaTabla, 'products/') === 0) {
            $tablaTallasUrl = asset_url('uploads/' . $limpiaTabla);
        } else {
            $tablaTallasUrl = asset_url('uploads/tabla_tallas/' . $limpiaTabla);
        }
    }
    $imagenes = $imagenes ?? [];
    $imagenesProducto = is_array($imagenes) ? $imagenes : [];
    $normalizarRutaImagenProducto = static function (string $ruta): string {
        $ruta = trim($ruta);

        if ($ruta === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $ruta) === 1) {
            return $ruta;
        }

        $rutaLimpia = ltrim($ruta, '/');

        if (strpos($rutaLimpia, 'public/assets/') === 0) {
            return base_url($rutaLimpia);
        }

        if (strpos($rutaLimpia, 'assets/') === 0) {
            return base_url('public/' . $rutaLimpia);
        }

        if (strpos($rutaLimpia, 'uploads/') === 0) {
            return asset_url($rutaLimpia);
        }

        if (strpos($rutaLimpia, 'productos/') === 0) {
            return asset_url('uploads/' . $rutaLimpia);
        }

        return asset_url('uploads/productos/' . $rutaLimpia);
    };
?>
<style>
.tabla-tallas-preview{position:relative;display:inline-block;margin-top:12px}
.tabla-tallas-preview img{width:120px;height:120px;border-radius:8px;border:1px solid #eee;object-fit:cover}
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
                        <input type="text" name="colores" id="colores" class="form-control" value="<?= e($coloresTexto); ?>" placeholder="Rojo, Azul, Verde">
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
                    <div class="col-12">
                        <label for="tablaTallasInput" class="form-label">Tabla de Tallas</label>
                        <input type="file" name="tabla_tallas" id="tablaTallasInput" accept=".jpg,.jpeg,.png,.webp,image/*" class="form-control">
                        <small class="form-text d-block mt-1">Formatos permitidos: JPG, PNG o WEBP.</small>
                        <?php if ($tablaTallasUrl !== ''): ?>
                            <div class="tabla-tallas-preview mt-2" data-has-tabla="1">
                                <img src="<?= e($tablaTallasUrl); ?>" alt="Tabla de tallas actual" width="120" height="120" style="border:1px solid #ccc;border-radius:4px;">
                                <?php if ($esEdicion && !empty($producto['id'])): ?>
                                    <button type="button"
                                            class="btn btn-danger btn-sm btn-delete-tabla-tallas btn-del-tabla"
                                            data-id="<?= (int) $producto['id']; ?>"
                                            data-url="<?= e(base_url('admin/productos/eliminar_tabla_tallas/' . (int) $producto['id'])); ?>"
                                            title="Eliminar tabla de tallas">&times;</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Imágenes del producto</h2>
                <?php if ($esEdicion && $productoId > 0): ?>
                    <p class="text-muted mb-3">Puedes subir hasta 10 imágenes en formatos JPG, PNG o WEBP. Peso máximo por archivo: 2 MB.</p>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="imagenProductoInput" class="form-label">Selecciona una imagen</label>
                            <input type="file" id="imagenProductoInput" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="form-text text-muted">La imagen se subirá inmediatamente después de presionar "Subir imagen".</small>
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="button"
                                    class="btn btn-primary"
                                    id="btnSubirImagenProducto"
                                    data-url="<?= e(base_url('admin/productos/subir_imagen/' . $productoId)); ?>"
                                    data-producto="<?= $productoId; ?>">
                                Subir imagen
                            </button>
                        </div>
                    </div>
                    <div class="mt-3" id="previewImagenProducto" style="display:none;">
                        <p class="text-muted small mb-2">Vista previa:</p>
                        <img src="" alt="Vista previa" class="img-fluid rounded border" style="max-height:200px;object-fit:cover;">
                    </div>
                    <p class="text-muted <?= $imagenesProducto !== [] ? 'd-none' : ''; ?> mt-4" id="mensajeImagenesVacias">No hay imágenes cargadas para este producto.</p>
                    <div class="row g-3 mt-0" id="listaImagenesProducto" data-max="10" data-url-eliminar-base="<?= e(rtrim(base_url('admin/productos/eliminar_imagen'), '/') . '/'); ?>">
                        <?php foreach ($imagenesProducto as $imagen):
                            $imagenId = (int) ($imagen['id'] ?? 0);
                            $imagenUrl = $normalizarRutaImagenProducto((string) ($imagen['ruta'] ?? ''));
                            $imagenNombre = (string) ($imagen['nombre'] ?? 'Imagen');
                            $esPrincipal = (int) ($imagen['es_principal'] ?? 0) === 1;
                            if ($imagenUrl === '') {
                                continue;
                            }
                        ?>
                            <div class="col-6 col-md-4 col-xl-3" data-imagen-id="<?= $imagenId; ?>">
                                <div class="border rounded position-relative overflow-hidden h-100">
                                    <img src="<?= e($imagenUrl); ?>" alt="<?= e($imagenNombre); ?>" class="img-fluid w-100" style="height:160px;object-fit:cover;">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 btn-eliminar-imagen-producto"
                                            data-url="<?= e(base_url('admin/productos/eliminar_imagen/' . $imagenId)); ?>"
                                            title="Eliminar imagen">
                                        &times;
                                    </button>
                                    <span class="badge bg-primary position-absolute start-0 bottom-0 m-2 badge-imagen-principal <?= $esPrincipal ? '' : 'd-none'; ?>">Principal</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Guarda el producto para habilitar la gestión de imágenes.</p>
                <?php endif; ?>
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
    const csrfTokenInput = document.querySelector('input[name="csrf_token"]');
    const csrfToken = csrfTokenInput ? csrfTokenInput.value : '';

    const tablaTallasButtons = document.querySelectorAll('.btn-delete-tabla-tallas');
    tablaTallasButtons.forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();

            if (!window.confirm('¿Eliminar esta imagen de tabla de tallas?')) {
                return;
            }

            const url = button.dataset.url || '';
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
                    body: new URLSearchParams({ csrf_token: csrfToken }),
                });

                if (!respuesta.ok) {
                    throw new Error('No se pudo eliminar la tabla de tallas.');
                }

                const data = await respuesta.json();
                if (!data.success) {
                    throw new Error(data.message || 'Ocurrió un error al eliminar la tabla de tallas.');
                }

                const contenedor = button.closest('.tabla-tallas-preview');
                if (contenedor) {
                    contenedor.remove();
                }
            } catch (error) {
                const mensaje = error instanceof Error ? error.message : 'Ocurrió un error al eliminar la tabla de tallas.';
                alert(mensaje);
            }
        });
    });

    const listaImagenes = document.getElementById('listaImagenesProducto');
    const mensajeImagenesVacias = document.getElementById('mensajeImagenesVacias');
    const btnSubirImagen = document.getElementById('btnSubirImagenProducto');
    const inputImagen = document.getElementById('imagenProductoInput');
    const previewContenedor = document.getElementById('previewImagenProducto');
    const previewImagen = previewContenedor ? previewContenedor.querySelector('img') : null;
    const maxImagenes = listaImagenes ? parseInt(listaImagenes.dataset.max || '10', 10) : 10;
    const urlEliminarBase = listaImagenes ? (listaImagenes.dataset.urlEliminarBase || '') : '';

    const actualizarMensajeVacio = () => {
        if (!listaImagenes || !mensajeImagenesVacias) {
            return;
        }
        const total = listaImagenes.querySelectorAll('[data-imagen-id]').length;
        if (total === 0) {
            mensajeImagenesVacias.classList.remove('d-none');
        } else {
            mensajeImagenesVacias.classList.add('d-none');
        }
    };

    const construirUrlEliminar = (id) => {
        if (!id) {
            return '';
        }
        const base = urlEliminarBase || '';
        if (base === '') {
            return '';
        }
        return base.replace(/\/$/, '') + '/' + id;
    };

    const actualizarBadgesPrincipal = (imagenes) => {
        if (!Array.isArray(imagenes) || !listaImagenes) {
            return;
        }

        const principales = new Set(
            imagenes
                .filter((imagen) => Number(imagen.es_principal) === 1)
                .map((imagen) => String(imagen.id))
        );

        listaImagenes.querySelectorAll('[data-imagen-id]').forEach((item) => {
            const imagenId = item.dataset.imagenId || '';
            const badge = item.querySelector('.badge-imagen-principal');
            if (!badge) {
                return;
            }

            if (principales.has(imagenId)) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
    };

    const registrarBotonEliminar = (button) => {
        if (!button) {
            return;
        }

        button.addEventListener('click', async (event) => {
            event.preventDefault();

            if (!window.confirm('¿Eliminar esta imagen?')) {
                return;
            }

            const url = button.dataset.url || '';
            if (url === '') {
                return;
            }

            button.disabled = true;

            try {
                const respuesta = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ csrf_token: csrfToken }),
                });

                if (!respuesta.ok) {
                    throw new Error('No se pudo eliminar la imagen.');
                }

                const data = await respuesta.json();
                if (!data.success) {
                    throw new Error(data.message || 'Ocurrió un error al eliminar la imagen.');
                }

                const tarjeta = button.closest('[data-imagen-id]');
                if (tarjeta) {
                    tarjeta.remove();
                }

                actualizarMensajeVacio();
                if (Array.isArray(data.imagenes)) {
                    actualizarBadgesPrincipal(data.imagenes);
                }
            } catch (error) {
                const mensaje = error instanceof Error ? error.message : 'Ocurrió un error al eliminar la imagen.';
                alert(mensaje);
            } finally {
                button.disabled = false;
            }
        });
    };

    if (listaImagenes) {
        listaImagenes.querySelectorAll('.btn-eliminar-imagen-producto').forEach((button) => registrarBotonEliminar(button));
        actualizarMensajeVacio();
    }

    if (inputImagen && previewContenedor && previewImagen) {
        inputImagen.addEventListener('change', () => {
            const archivos = inputImagen.files;
            if (archivos && archivos[0]) {
                const lector = new FileReader();
                lector.onload = (evento) => {
                    previewImagen.src = evento.target && evento.target.result ? evento.target.result : '';
                    previewContenedor.style.display = 'block';
                };
                lector.readAsDataURL(archivos[0]);
            } else {
                previewImagen.src = '';
                previewContenedor.style.display = 'none';
            }
        });
    }

    if (btnSubirImagen && inputImagen && listaImagenes) {
        btnSubirImagen.addEventListener('click', async (event) => {
            event.preventDefault();

            const totalActual = listaImagenes.querySelectorAll('[data-imagen-id]').length;
            if (totalActual >= maxImagenes) {
                alert('Has alcanzado el número máximo de imágenes permitidas.');
                return;
            }

            if (!inputImagen.files || inputImagen.files.length === 0) {
                alert('Selecciona una imagen antes de subir.');
                return;
            }

            const url = btnSubirImagen.dataset.url || '';
            if (url === '') {
                alert('No se pudo determinar la URL de subida.');
                return;
            }

            const archivo = inputImagen.files[0];
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('imagen_producto', archivo);

            btnSubirImagen.disabled = true;
            btnSubirImagen.textContent = 'Subiendo...';

            try {
                const respuesta = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (!respuesta.ok) {
                    throw new Error('No se pudo subir la imagen.');
                }

                const data = await respuesta.json();
                if (!data.success || !data.imagen) {
                    throw new Error(data.message || 'Ocurrió un error al subir la imagen.');
                }

                const imagen = data.imagen;
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-xl-3';
                col.dataset.imagenId = String(imagen.id || '');
                const nombreImagen = (imagen.nombre || 'Imagen').replace(/"/g, '&quot;');
                const urlEliminar = construirUrlEliminar(imagen.id || '');
                col.innerHTML = `
                    <div class="border rounded position-relative overflow-hidden h-100">
                        <img src="${imagen.url || ''}" alt="${nombreImagen}" class="img-fluid w-100" style="height:160px;object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 btn-eliminar-imagen-producto" data-url="${urlEliminar}" title="Eliminar imagen">&times;</button>
                        <span class="badge bg-primary position-absolute start-0 bottom-0 m-2 badge-imagen-principal ${Number(imagen.es_principal) === 1 ? '' : 'd-none'}">Principal</span>
                    </div>
                `;

                const botonEliminar = col.querySelector('.btn-eliminar-imagen-producto');
                if (botonEliminar) {
                    registrarBotonEliminar(botonEliminar);
                }

                listaImagenes.appendChild(col);
                actualizarMensajeVacio();

                if (Array.isArray(data.imagenes)) {
                    actualizarBadgesPrincipal(data.imagenes);
                }

                inputImagen.value = '';
                if (previewContenedor) {
                    previewContenedor.style.display = 'none';
                }
            } catch (error) {
                const mensaje = error instanceof Error ? error.message : 'Ocurrió un error al subir la imagen.';
                alert(mensaje);
            } finally {
                btnSubirImagen.disabled = false;
                btnSubirImagen.textContent = 'Subir imagen';
            }
        });
    }
});
</script>
