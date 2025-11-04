<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h2 class="h5 mb-0"><?= $categoriaEditar ? 'Editar categoría' : 'Nueva categoría'; ?></h2>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/categorias/guardar'); ?>" method="post" class="row g-3">
                    <?= csrf_field(); ?>
                    <?php if ($categoriaEditar): ?>
                        <input type="hidden" name="id" value="<?= (int) $categoriaEditar['id']; ?>">
                    <?php endif; ?>
                    <div class="col-12">
                        <label for="categoria_nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="categoria_nombre" class="form-control" value="<?= e($categoriaEditar['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="categoria_descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="categoria_descripcion" rows="3" class="form-control"><?= e($categoriaEditar['descripcion'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="categoria_activo" name="activo" value="1" <?= (int) ($categoriaEditar['activo'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="categoria_activo">Categoría activa</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><?= $categoriaEditar ? 'Guardar categoría' : 'Crear categoría'; ?></button>
                        <?php if ($categoriaEditar): ?>
                            <a href="<?= base_url('admin/categorias'); ?>" class="btn btn-link">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Listado de categorías</h2>
                <span class="badge bg-secondary-subtle text-secondary-emphasis"><?= count($categorias); ?> registradas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Subcategorías</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($categorias === []): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay categorías registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($categoria['nombre'] ?? ''); ?></td>
                                        <td><?= (int) ($categoria['total_subcategorias'] ?? 0); ?></td>
                                        <td>
                                            <?php if ((int) ($categoria['activo'] ?? 0) === 1): ?>
                                                <span class="badge bg-success-subtle text-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/categorias?categoria=' . (int) ($categoria['id'] ?? 0)); ?>">Editar</a>
                                                <form action="<?= base_url('admin/categorias/eliminar'); ?>" method="post" onsubmit="return confirm('¿Eliminar categoría?');">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= (int) ($categoria['id'] ?? 0); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h2 class="h5 mb-0"><?= $subcategoriaEditar ? 'Editar subcategoría' : 'Nueva subcategoría'; ?></h2>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/categorias/subcategorias/guardar'); ?>" method="post" class="row g-3">
                    <?= csrf_field(); ?>
                    <?php if ($subcategoriaEditar): ?>
                        <input type="hidden" name="id" value="<?= (int) $subcategoriaEditar['id']; ?>">
                    <?php endif; ?>
                    <div class="col-12">
                        <label for="subcategoria_categoria" class="form-label">Categoría</label>
                        <select name="categoria_id" id="subcategoria_categoria" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) ($categoria['id'] ?? 0); ?>" <?= (int) ($subcategoriaEditar['categoria_id'] ?? 0) === (int) ($categoria['id'] ?? 0) ? 'selected' : ''; ?>><?= e($categoria['nombre'] ?? ''); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="subcategoria_nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="subcategoria_nombre" class="form-control" value="<?= e($subcategoriaEditar['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="subcategoria_descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="subcategoria_descripcion" rows="3" class="form-control"><?= e($subcategoriaEditar['descripcion'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="subcategoria_activo" name="activo" value="1" <?= (int) ($subcategoriaEditar['activo'] ?? 1) === 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="subcategoria_activo">Subcategoría activa</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><?= $subcategoriaEditar ? 'Guardar subcategoría' : 'Crear subcategoría'; ?></button>
                        <?php if ($subcategoriaEditar): ?>
                            <a href="<?= base_url('admin/categorias'); ?>" class="btn btn-link">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Listado de subcategorías</h2>
                <span class="badge bg-secondary-subtle text-secondary-emphasis"><?= count($subcategorias); ?> registradas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($subcategorias === []): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay subcategorías registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subcategorias as $subcategoria): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= e($subcategoria['nombre'] ?? ''); ?></td>
                                        <td><?= e($subcategoria['categoria_nombre'] ?? ''); ?></td>
                                        <td>
                                            <?php if ((int) ($subcategoria['activo'] ?? 0) === 1): ?>
                                                <span class="badge bg-success-subtle text-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/categorias?subcategoria=' . (int) ($subcategoria['id'] ?? 0)); ?>">Editar</a>
                                                <form action="<?= base_url('admin/categorias/subcategorias/eliminar'); ?>" method="post" onsubmit="return confirm('¿Eliminar subcategoría?');">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= (int) ($subcategoria['id'] ?? 0); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
