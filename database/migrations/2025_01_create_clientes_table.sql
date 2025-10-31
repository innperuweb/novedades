CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30),
    direccion VARCHAR(255),
    distrito VARCHAR(100),
    referencia VARCHAR(255),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE ordenes
    ADD COLUMN id_cliente INT NULL AFTER id,
    ADD CONSTRAINT fk_ordenes_cliente
        FOREIGN KEY (id_cliente) REFERENCES clientes(id);
