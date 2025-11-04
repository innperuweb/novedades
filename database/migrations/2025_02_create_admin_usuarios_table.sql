CREATE TABLE IF NOT EXISTS admin_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    pass_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL DEFAULT 'admin',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO admin_usuarios (nombre, email, pass_hash, rol, activo, creado_en)
SELECT 'Administrador', 'admin@site.com', '$2y$12$ZGhtEM3Ue6KsanflQ.Yf3uHVSyt7qoWUMXu6I/LQZz0YVlW6sUJue', 'admin', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM admin_usuarios WHERE email = 'admin@site.com');
