CREATE TABLE IF NOT EXISTS producto_categorias_web (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    seccion ENUM('tienda', 'novedades', 'ofertas', 'populares', 'por_mayor') NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
