-- Base de datos: universidad

CREATE DATABASE IF NOT EXISTS universidad;
USE universidad;

-- Tabla de Usuarios (Profesores, Estudiantes, Admins)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('profesor', 'estudiante', 'admin') NOT NULL,
    foto VARCHAR(255) DEFAULT 'default_avatar.png',
    identificacion VARCHAR(20) DEFAULT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    ciudad VARCHAR(100) DEFAULT NULL,
    departamento VARCHAR(100) DEFAULT NULL,
    correo_institucional VARCHAR(100) DEFAULT NULL,
    programa_academico VARCHAR(100) DEFAULT NULL,
    semestre VARCHAR(20) DEFAULT NULL,
    codigo_estudiantil VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Materias (Asignaturas/Cursos)
CREATE TABLE IF NOT EXISTS materias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    profesor_id INT,
    descripcion TEXT,
    FOREIGN KEY (profesor_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabla de Matriculas (Relación Estudiante - Materia)
CREATE TABLE IF NOT EXISTS matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    materia_id INT NOT NULL,
    periodo VARCHAR(50) NOT NULL, -- Ej: 2024-1
    FOREIGN KEY (estudiante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE
);

-- Tabla de Notas
CREATE TABLE IF NOT EXISTS notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula_id INT NOT NULL,
    corte VARCHAR(50) NOT NULL, -- Ej: 'Parcial 1', 'Final'
    valor DECIMAL(4, 2) NOT NULL, -- 0.00 a 10.00 (o 5.00)
    observacion TEXT, -- Observación del docente
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (matricula_id) REFERENCES matriculas(id) ON DELETE CASCADE
);

-- Tabla de Asistencia
CREATE TABLE IF NOT EXISTS asistencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula_id INT NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('Presente', 'Ausente', 'Justificado') NOT NULL,
    FOREIGN KEY (matricula_id) REFERENCES matriculas(id) ON DELETE CASCADE
);

-- Tabla de Configuración (Para rangos de notas y permisos)
CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor VARCHAR(255) NOT NULL
);

-- Insertar configuraciones por defecto
INSERT INTO configuracion (clave, valor) VALUES 
('min_nota', '0.0'),
('max_nota', '5.0'),
('edicion_notas_activa', '1'); -- 1 = Activo, 0 = Inactivo
