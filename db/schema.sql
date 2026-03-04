-- SQL para TechTrack: Sistema de Gestión de Servicios Técnicos

CREATE DATABASE IF NOT EXISTS techtrack_db;
USE techtrack_db;

-- Tabla: Clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    direccion TEXT DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Ordenes de Servicio
CREATE TABLE IF NOT EXISTS ordenes_servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    equipo VARCHAR(100) NOT NULL,
    descripcion_problema TEXT NOT NULL,
    estado ENUM('Pendiente', 'En proceso', 'Finalizado') DEFAULT 'Pendiente',
    fecha_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATETIME DEFAULT NULL,
    notas_tecnicas TEXT DEFAULT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de Ejemplo para Pruebas Iniciales
INSERT INTO clientes (nombre, telefono, email, direccion) VALUES 
('Juan Pérez', '11-2233-4455', 'juan@email.com', 'Av. Rivadavia 1234, CABA'),
('María García', '11-5566-7788', 'm.garcia@email.com', 'Calle Balcarce 50, CABA'),
('Roberto Gómez', '11-9900-1122', 'roberto@email.com', 'Belgrano 456, CABA');

INSERT INTO ordenes_servicio (cliente_id, equipo, descripcion_problema, estado) VALUES
(1, 'Notebook HP Pavilion', 'No enciende, posible falla en el jack de carga.', 'Pendiente'),
(2, 'PC Escritorio Gamer', 'Limpieza y cambio de pasta térmica.', 'Pendiente'),
(3, 'Impresora Epson L3110', 'Atasco de papel recurrente.', 'En proceso');
