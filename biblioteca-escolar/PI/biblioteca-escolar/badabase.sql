-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS biblioteca_escolar
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE biblioteca_escolar;

-- Tabla: usuarios
CREATE TABLE usuarios (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  tipo_usuario ENUM('estudiante','profesor','administrador') NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: libros
CREATE TABLE libros (
  id INT(11) NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(255) NOT NULL,
  autor VARCHAR(255) NOT NULL,
  anio YEAR(4) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  imagen VARCHAR(255) DEFAULT NULL,
  cantidad_disponible INT(10) UNSIGNED NOT NULL,
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  estado ENUM('activo','anulado') DEFAULT 'activo',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: prestamos
CREATE TABLE prestamos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  usuario_id INT(11) NOT NULL,
  libro_id INT(11) NOT NULL,
  fecha_prestamo DATE NOT NULL DEFAULT CURDATE(),
  fecha_devolucion DATE NOT NULL,
  fecha_devolucion_real DATE DEFAULT NULL,
  cantidad INT(11) NOT NULL,
  observaciones TEXT DEFAULT NULL,
  estado ENUM('activo','devuelto','anulado') NOT NULL,
  PRIMARY KEY (id),
  KEY usuario_id (usuario_id),
  KEY libro_id (libro_id),
  CONSTRAINT fk_prestamos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_prestamos_libro FOREIGN KEY (libro_id) REFERENCES libros (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar un usuario administrador con contraseña:12345
INSERT INTO usuarios (nombre, email, password, tipo_usuario)
VALUES (
  'Administrador',
  'admin@gmail.com',
  '$2y$10$vWQlI/SuaPHkJrIqVxAY7.MllXa.EvxYwB5c5zYC3uFGIGw5aMu66',
  'administrador'
);
