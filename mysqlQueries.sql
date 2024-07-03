-- Crear una nueva base de datos
CREATE DATABASE tp_la_comanda;

-- Usar la base de datos
USE tp_la_comanda;

-- Crear una tabla

CREATE TABLE usuarios(
	id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(50) NOT NULL,
    rol ENUM('mozo', 'bartender', 'socio', 'cervecero', 'cocinero') NOT NULL,
    sector ENUM('cocina', 'barra de choperas', 'barra de tragos y vinos', 'candy bar') NOT NULL,
    tiempo_estimado INT NOT NULL,
    fecha_ingreso DATE NOT NULL,
	estado ENUM('activo', 'suspendido') NOT NULL DEFAULT 'activo'
);

CREATE TABLE pedidos(
	id VARCHAR(10),
    nombre_cliente VARCHAR(50) NOT NULL,
	id_mesa VARCHAR(50),
    estado ENUM('en preparacion', 'listo para servir', 'en espera'),
    tiempo_estimado INT,
	producto VARCHAR(255) NOT NULL,
    sector VARCHAR(100) NOT NULL,
);

CREATE TABLE clientes(
	nombre VARCHAR(255) NOT NULL,
    id_pedido INT NOT NULL,
    id_mesa INT NOT NULL
);

CREATE TABLE mesas(
	id VARCHAR(10),
    estado ENUM('cliente esperando pedido', 'cliente comiendo', 'cliente pagando', 'cerrada') NOT NULL DEFAULT 'cerrada',
    total_facturado DECIMAL NOT NULL DEFAULT 0,
    importe_mayor DECIMAL NOT NULL DEFAULT 0,
    importe_menor DECIMAL NOT NULL DEFAULT 0,
    cantidad_usada INT DEFAULT 0
);

CREATE TABLE productos(
    nombre VARCHAR(50) NOT NULL,
    categoria ENUM('trago', 'vino', 'cerveza', 'comida', 'postre') NOT NULL,
    sector ENUM('barra de tragos y vinos', 'barra de choperas', 'cocina', 'candy bar') NOT NULL,
    precio DECIMAL(10, 2) NOT NULL
);