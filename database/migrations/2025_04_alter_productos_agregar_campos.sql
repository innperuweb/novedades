-- Ajustes de la tabla de productos
ALTER TABLE productos
  ADD COLUMN IF NOT EXISTS sku VARCHAR(100) NULL AFTER nombre,
  ADD COLUMN IF NOT EXISTS stock INT NOT NULL DEFAULT 0 AFTER precio,
  ADD COLUMN IF NOT EXISTS estado TINYINT(1) NOT NULL DEFAULT 1 AFTER stock,
  ADD COLUMN IF NOT EXISTS visible TINYINT(1) NOT NULL DEFAULT 1 AFTER estado;

ALTER TABLE productos
  ADD COLUMN IF NOT EXISTS colores TEXT NULL AFTER visible,
  ADD COLUMN IF NOT EXISTS tallas TEXT NULL AFTER colores;

-- Tabla de imágenes de productos (crear si no existe)
CREATE TABLE IF NOT EXISTS productos_imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto_id INT NOT NULL,
  ruta VARCHAR(255) NOT NULL,
  es_principal TINYINT(1) NOT NULL DEFAULT 0,
  orden INT NOT NULL DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX IF NOT EXISTS idx_prod_img_prod ON productos_imagenes(producto_id);

-- Asegurar columnas clave en tablas existentes de imágenes
ALTER TABLE producto_imagenes
  ADD COLUMN IF NOT EXISTS es_principal TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS orden INT NOT NULL DEFAULT 0;

ALTER TABLE productos_imagenes
  ADD COLUMN IF NOT EXISTS es_principal TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS orden INT NOT NULL DEFAULT 0;

CREATE INDEX IF NOT EXISTS idx_producto_imagenes_producto ON producto_imagenes(producto_id);
