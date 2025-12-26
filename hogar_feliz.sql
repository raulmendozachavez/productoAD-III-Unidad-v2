CREATE DATABASE hogar_feliz;
USE hogar_feliz;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(15) NULL,
    direccion VARCHAR(200) NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    remember_token VARCHAR(100) NULL
);

-- Tabla para tokens de restablecimiento de contraseña
CREATE TABLE password_reset_tokens (
    email VARCHAR(100) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- Tabla para sesiones
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

-- Tabla de mascotas
CREATE TABLE mascotas (
    id_mascota BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    tipo ENUM('perros', 'gatos', 'otros') NOT NULL,
    raza VARCHAR(50),
    edad VARCHAR(20),
    descripcion TEXT,
    imagen VARCHAR(255),
    estado ENUM('disponible', 'adoptado', 'en_proceso') DEFAULT 'disponible',
    fecha_ingreso DATE,
    es_rescate BOOLEAN DEFAULT FALSE
);

-- Tabla de adopciones
CREATE TABLE adopciones (
    id_adopcion BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_usuario BIGINT UNSIGNED,
    id_mascota BIGINT UNSIGNED,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion TIMESTAMP NULL,
    estado ENUM('pendiente', 'aprobada', 'rechazada', 'completada') DEFAULT 'pendiente',
    notas TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota) ON DELETE CASCADE
);

-- Tabla de productos (tienda)
CREATE TABLE productos (
    id_producto BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50),
    imagen VARCHAR(255),
    stock INT DEFAULT 100
);

-- Tabla de carrito
CREATE TABLE carrito (
    id_carrito BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_usuario BIGINT UNSIGNED,
    id_producto BIGINT UNSIGNED,
    cantidad INT DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);

-- Tabla de pedidos
CREATE TABLE pedidos (
    id_pedido BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_usuario BIGINT UNSIGNED,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'procesando', 'enviado', 'entregado') DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    direccion_envio VARCHAR(200),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de detalle de pedidos
CREATE TABLE detalle_pedidos (
    id_detalle BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_pedido BIGINT UNSIGNED NOT NULL,
    id_producto BIGINT UNSIGNED NULL,
    producto_nombre VARCHAR(100) NULL,
    producto_categoria VARCHAR(50) NULL,
    producto_imagen VARCHAR(255) NULL,

    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE SET NULL
);

-- Tabla de casos de rescate
CREATE TABLE casos_rescate (
    id_rescate BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_mascota BIGINT UNSIGNED,
    situacion TEXT NOT NULL,
    historia TEXT NOT NULL,
    tratamiento TEXT,
    urgencia ENUM('baja', 'media', 'alta') DEFAULT 'media',
    fecha_rescate DATE,
    FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota) ON DELETE CASCADE
);
CREATE TABLE auditoria (
    id_auditoria BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_usuario BIGINT UNSIGNED NULL,
    nombre_usuario VARCHAR(50) NOT NULL,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    datos_anteriores JSON,
    datos_nuevos JSON,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_hora),
    INDEX idx_modulo (modulo)
);

-- ========================================
-- INSERTAR DATOS DE EJEMPLO
-- ========================================

INSERT INTO usuarios (nombre_usuario, nombre_completo, email, telefono, direccion, password, rol)
VALUES
('usuario123', 'Juan Pérez', 'usuario@email.com', '987654321', 'Av. Los Olivos 123', '12345', 'usuario'),
('admin', 'Administrador', 'admin@email.com', '999666333', 'Oficina Central', '12345', 'admin');

-- Mascotas de ejemplo (PERROS)
INSERT INTO mascotas (nombre, tipo, raza, edad, descripcion, imagen, estado, fecha_ingreso) VALUES
('Max', 'perros', 'Labrador', '3 años', 'Perro muy juguetón y amigable, perfecto para familias con niños. Le encanta correr y jugar en el parque.', 'perro1.jpg', 'disponible', '2024-01-15'),
('Rocky', 'perros', 'Pastor Alemán', '5 años', 'Guardián leal y protector. Entrenado en obediencia básica, ideal para casas con jardín.', 'perro2.jpg', 'disponible', '2024-02-20'),
('Bella', 'perros', 'Golden Retriever', '2 años', 'Dulce y cariñosa, ama a los niños. Muy tranquila en casa pero le gusta pasear.', 'perro3.jpg', 'disponible', '2024-03-10');

-- Mascotas de ejemplo (GATOS)
INSERT INTO mascotas (nombre, tipo, raza, edad, descripcion, imagen, estado, fecha_ingreso) VALUES
('Luna', 'gatos', 'Persa', '2 años', 'Gata tranquila y elegante. Le gusta dormir en lugares cálidos y recibir mimos.', 'gato1.jpg', 'disponible', '2024-01-25'),
('Michi', 'gatos', 'Siamés', '1 año', 'Muy activo y juguetón. Le encanta trepar y explorar cada rincón de la casa.', 'gato2.jpg', 'disponible', '2024-02-15'),
('Garfield', 'gatos', 'Naranja', '4 años', 'Gato tranquilo que disfruta de la comida y las siestas. Perfecto para apartamentos.', 'gato3.jpg', 'disponible', '2024-03-05');

-- Mascotas de ejemplo (OTROS)
INSERT INTO mascotas (nombre, tipo, raza, edad, descripcion, imagen, estado, fecha_ingreso) VALUES
('Copito', 'otros', 'Holland Lop', '1 año', 'Conejo súper tierno y cariñoso. Le gusta que lo acaricien y es muy sociable.', 'conejo1.jpg', 'disponible', '2024-02-01'),
('Tambor', 'otros', 'Belier', '6 meses', 'Juguetón y curioso. Le encanta explorar y saltar por toda la casa.', 'conejo2.jpg', 'disponible', '2024-02-28'),
('Piolín', 'otros', 'Canario', '1 año', 'Canta hermoso por las mañanas. Muy alegre y colorido, perfecto para hogares tranquilos.', 'ave1.jpg', 'disponible', '2024-03-15'),
('Kiwi', 'otros', 'Periquito', '2 años', 'Sociable y puede aprender a imitar sonidos. Le gusta interactuar con las personas.', 'ave2.jpg', 'disponible', '2024-03-20'),
('Stuart', 'otros', 'Sirio', '6 meses', 'Pequeño y adorable. Perfecto para niños, requiere poco espacio y cuidados básicos.', 'hamster1.jpg', 'disponible', '2024-03-25');

-- Productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, categoria, imagen, stock) VALUES
('Alimento Premium para Perros', 'Croquetas nutritivas con carne de pollo real. Bolsa de 15kg. Ideal para perros adultos de todas las razas.', 120.00, 'alimento', 'comida-perro.jpg', 50),
('Alimento para Gatos', 'Croquetas con salmón y atún. Bolsa de 10kg. Favorece el pelaje brillante y salud digestiva.', 95.00, 'alimento', 'comida-gato.jpg', 45),
('Set de Juguetes para Perro', 'Pack de 5 juguetes resistentes: pelota, cuerda, hueso masticable y más. Perfecto para mantener a tu perro activo.', 45.00, 'juguete', 'juguete-perro.jpg', 30),
('Rascador para Gatos', 'Torre rascadora de 80cm con plataformas y juguetes colgantes. Protege tus muebles mientras tu gato juega.', 180.00, 'juguete', 'rascador-gato.jpg', 20),
('Collar Ajustable con Correa', 'Collar acolchado de nylon resistente con correa de 1.5m. Disponible en varios colores. Talla M.', 35.00, 'accesorio', 'collar-perro.jpg', 60),
('Cama Acolchada para Mascotas', 'Cama ortopédica de memory foam. Funda lavable. Tamaño grande (90x70cm). Perfecta para el descanso.', 150.00, 'accesorio', 'cama-mascota.jpg', 25),
('Shampoo Hipoalergénico', 'Shampoo suave sin parabenos. Con aloe vera y avena. 500ml. Ideal para pieles sensibles.', 28.00, 'higiene', 'shampoo.jpg', 100),
('Arena Aglomerante para Gatos', 'Arena premium con control de olores. 10kg. Aglomerante y fácil de limpiar. Libre de polvo.', 42.00, 'higiene', 'arena-gato.jpg', 70),
('Suplemento Vitamínico', 'Multivitamínico completo para mascotas. 60 tabletas masticables. Fortalece el sistema inmune.', 65.00, 'salud', 'vitaminas.jpg', 40),
('Tratamiento Antipulgas', 'Pipeta antipulgas y garrapatas de larga duración. Pack de 3 unidades. Protección por 3 meses.', 85.00, 'salud', 'antipulgas.jpg', 35);

-- Casos de rescate de ejemplo
INSERT INTO casos_rescate (id_mascota, situacion, historia, tratamiento, urgencia, fecha_rescate) VALUES
(1, 'Encontrado abandonado en una carretera con desnutrición severa.',
 'Max fue rescatado hace 2 semanas en condiciones deplorables. Estaba extremadamente delgado y asustado. Ahora está recibiendo tratamiento veterinario y está ganando peso gradualmente. A pesar de su pasado, muestra signos de querer confiar en las personas nuevamente. Necesita un hogar paciente que le ayude a recuperar la confianza.',
 'En rehabilitación nutricional y terapia de socialización.', 'alta', '2024-03-01'),

(2, 'Rescatado de una situación de maltrato físico.',
 'Rocky fue víctima de maltrato físico por parte de sus antiguos dueños. Llegó al refugio con múltiples lesiones y un miedo extremo a los humanos. Ha pasado 3 meses en rehabilitación física y emocional. Poco a poco está aprendiendo que no todas las personas son malas. Tiene algunos problemas de movilidad en su pata trasera pero eso no le impide ser cariñoso.',
 'Fisioterapia semanal y terapia conductual.', 'media', '2024-02-01'),

(3, 'Abandonada por ser considerada \"muy vieja\".',
 'Bella fue dejada en la puerta del refugio con una nota que decía \"ya no la queremos, es muy vieja\". Tiene solo 2 años y está en perfecto estado de salud. Es una perra noble, tranquila y educada. Simplemente fue descartada por una familia que no valoró su lealtad.',
 'Chequeos veterinarios de rutina.', 'baja', '2024-02-15'),

(4, 'Encontrada en la calle con infección ocular grave.',
 'Luna fue encontrada vagando con una infección ocular tan severa que estuvo a punto de perder la vista. Gracias a un tratamiento intensivo, logró recuperarse casi completamente, aunque su ojo izquierdo quedó con visión limitada.',
 'Seguimiento oftalmológico mensual.', 'media', '2024-01-20'),

(5, 'Abandonado por supersticiones sobre gatos negros.',
 'Michi fue abandonado en un basurero debido a creencias supersticiosas. Es un gato extremadamente cariñoso que solo busca amor y un hogar cálido.',
 'Desparasitación y vacunación completa.', 'alta', '2024-03-10'),

(7, 'Abandonado después de Semana Santa.',
 'Copito fue un \"regalo\" de Semana Santa que terminó abandonado en un parque cuando la familia se cansó de él. Es tímido pero dulce cuando gana confianza.',
 'Dieta especial y socialización.', 'media', '2024-03-15');