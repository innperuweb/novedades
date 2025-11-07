-- Tabla principal de órdenes
CREATE TABLE IF NOT EXISTS ordenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nro_orden VARCHAR(20) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    nombre VARCHAR(100),
    apellidos VARCHAR(100),
    email VARCHAR(150),
    telefono VARCHAR(50),
    dni VARCHAR(20),
    direccion TEXT,
    distrito VARCHAR(100),
    referencia TEXT,
    metodo_envio VARCHAR(100),
    metodo_envio_texto VARCHAR(150),
    costo_envio DECIMAL(10,2) DEFAULT 0,
    metodo_pago VARCHAR(100),
    subtotal DECIMAL(10,2),
    total DECIMAL(10,2),
    estado ENUM('Pendiente','Pagado','Entregado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Detalle de productos por orden
CREATE TABLE IF NOT EXISTS orden_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_id INT,
    producto_id INT,
    nombre_producto VARCHAR(255),
    color VARCHAR(50),
    talla VARCHAR(20),
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    subtotal DECIMAL(10,2),
    FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relación entre productos y secciones web
CREATE TABLE IF NOT EXISTS producto_categorias_web (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    seccion ENUM('tienda', 'novedades', 'ofertas', 'populares', 'por_mayor') NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
