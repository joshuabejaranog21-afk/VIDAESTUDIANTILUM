-- Tabla de colaboradores para el módulo Pulso
CREATE TABLE pulso_colaboradores(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200) NOT NULL,
    cargo VARCHAR(150) NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE DEFAULT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = INNODB;

-- Insertar datos de ejemplo
INSERT INTO pulso_colaboradores (nombre, cargo, imagen, fecha_inicio, fecha_fin, estado) VALUES
('Juan Pérez González', 'Desarrollador Frontend', 'juan_perez.jpg', '2023-01-15', '2024-06-30', 'inactivo'),
('María González López', 'Coordinadora de Proyecto', 'maria_gonzalez.jpg', '2023-03-01', NULL, 'activo'),
('Carlos Ramírez', 'Diseñador UX/UI', 'carlos_ramirez.jpg', '2022-08-01', '2023-12-31', 'inactivo'),
('Ana Martínez', 'Desarrolladora Backend', 'ana_martinez.jpg', '2024-01-10', NULL, 'activo'),
('Luis Hernández', 'Analista de Datos', 'luis_hernandez.jpg', '2023-06-15', '2024-08-20', 'inactivo'),
('Sofia Torres', 'Product Manager', 'sofia_torres.jpg', '2024-02-01', NULL, 'activo');
