<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = 'sistema_compra_venta';

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar si ya existe la base de datos 
$db_check = $conn->query("SHOW DATABASES LIKE '$db'");
if ($db_check->num_rows == 0) {
    $sql = "CREATE DATABASE IF NOT EXISTS sistema_compra_venta";
    if ($conn->query($sql) === TRUE) {
        //echo "Database creado";
    } else {
        //echo "Error database: " . $conn->error . "<br>";
    }

    $conn->select_db("sistema_compra_venta");

    $sql = <<<SQL
    -- EMPLEADOS
    CREATE TABLE empleados(
        id VARCHAR(10) PRIMARY KEY,
        nombres VARCHAR(50) NOT NULL,
        apellidos VARCHAR(50) NOT NULL,
        sexo ENUM('masculino', 'femenino') NOT NULL,
        fecha_nacimiento DATE,
        tipo_documento ENUM('DNI', 'CE', 'pasaporte'),
        numero_documento VARCHAR(20) NOT NULL,
        foto VARCHAR(255) NOT NULL,
        direccion VARCHAR(50) NOT NULL,
        telefono VARCHAR(9) CHECK (telefono REGEXP '^[0-9]{9}$'),
        email VARCHAR(100) UNIQUE NOT NULL,
        estado ENUM('activo', 'no activo') NOT NULL,
        tipo ENUM('vendedor', 'cajero', 'administrador') NOT NULL
    );

    -- USUARIOS
    CREATE TABLE usuarios(
        id INT AUTO_INCREMENT PRIMARY KEY,
        empleado_id VARCHAR(10),
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        FOREIGN KEY (empleado_id) REFERENCES empleados(id)
    );

    -- CLIENTES
    CREATE TABLE clientes(
        id VARCHAR(10) PRIMARY KEY,
        nombres VARCHAR(50) NOT NULL,
        apellidos VARCHAR(50) NOT NULL,
        sexo ENUM('masculino', 'femenino') NOT NULL,
        tipo_documento ENUM('DNI', 'CE', 'pasaporte'),
        numero_documento VARCHAR(20) NOT NULL,
        direccion VARCHAR(50) NOT NULL,
        telefono VARCHAR(9) CHECK (telefono REGEXP '^[0-9]{9}$'),
        email VARCHAR(100) UNIQUE NOT NULL
    );

    -- PROVEEDORES
    CREATE TABLE proveedores (
        id VARCHAR(10) PRIMARY KEY,
        razon_social VARCHAR(100) NOT NULL,
        ruc VARCHAR(20) NOT NULL,
        direccion VARCHAR(100) NOT NULL,
        telefono VARCHAR(9) CHECK (telefono REGEXP '^[0-9]{9}$'),
        email VARCHAR(100) UNIQUE NOT NULL,
        url VARCHAR(255)
    );

    -- CATEGORIAS
    CREATE TABLE categorias (
        id VARCHAR(10) PRIMARY KEY,
        nombre_categoria VARCHAR(50) NOT NULL,
        descripcion TEXT
    );

    -- COMPRAS
    CREATE TABLE compras (
        id VARCHAR(10) PRIMARY KEY,
        tipo_comprobante ENUM('factura', 'boleta') NOT NULL,
        nro_comprobante VARCHAR(20) NOT NULL,
        fecha_emision DATE NOT NULL,
        proveedor_id VARCHAR(10) NOT NULL,
        FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
    );

    -- PRODUCTOS
    CREATE TABLE productos(
        id VARCHAR(10) PRIMARY KEY,
        descripcion VARCHAR(1000) NOT NULL,
        foto VARCHAR(255) NOT NULL,
        marca VARCHAR(20) NOT NULL,
        modelo VARCHAR(20) NOT NULL,
        stock_inicial INT NOT NULL,
        stock_actual INT NOT NULL CHECK (stock_actual >= 0),
        categoria_id VARCHAR(10),
        proveedor_id VARCHAR(10),
        precio INT NOT NULL,
        compra_id VARCHAR(10),
        FOREIGN KEY (categoria_id) REFERENCES categorias(id),
        FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
        FOREIGN KEY (compra_id) REFERENCES compras(id)
    );

    -- ORDEN DE VENTA
    CREATE TABLE orden_venta(
        id VARCHAR(10) PRIMARY KEY,
        empleado_id VARCHAR(10),
        fecha DATE NOT NULL,
        FOREIGN KEY (empleado_id) REFERENCES empleados(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
    );

    -- DETALLE DE ORDEN DE VENTA
    CREATE TABLE detalle_orden(
        id VARCHAR(10) PRIMARY KEY,
        id_orden VARCHAR(10),
        id_producto VARCHAR(10),
        cantidad INT NOT NULL,
        precio_unitario INT NOT NULL,
        FOREIGN KEY (id_orden) REFERENCES orden_venta(id),
        FOREIGN KEY (id_producto) REFERENCES productos(id)
    );

    -- VENTAS
    CREATE TABLE ventas (
        id VARCHAR(10) PRIMARY KEY,
        tipo_comprobante ENUM('factura', 'boleta') NOT NULL,
        nro_comprobante VARCHAR(20) NOT NULL,
        fecha_emision DATE NOT NULL,
        proveedor_id VARCHAR(10),
        cliente_id VARCHAR(10),
        FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    );

    -- DETALLE DE VENTAS
    CREATE TABLE detalle_venta (
        id VARCHAR(10) PRIMARY KEY,
        venta_id VARCHAR(10),
        producto_id VARCHAR(10),
        cantidad INT NOT NULL,
        precio_unitario INT NOT NULL,
        FOREIGN KEY (venta_id) REFERENCES ventas(id),
        FOREIGN KEY (producto_id) REFERENCES productos(id)
    );
    SQL;

    if ($conn->multi_query($sql) === TRUE) {
        //echo "Tables created successfully<br>";

        // Ensure all results are processed
        while ($conn->next_result()) {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        }
    } else {
        //echo "Error creating tables: " . $conn->error . "<br>";
    }

    // Insert initial data
    $sql = <<<SQL
    INSERT INTO empleados (id, nombres, apellidos, sexo, fecha_nacimiento, tipo_documento, numero_documento, foto, direccion, telefono, email, estado, tipo)
    VALUES
        ('E001', 'Juan', 'Pérez', 'masculino', '1985-03-15', 'DNI', '12345678', 'juan_perez.png', 'Av. Principal 123', '987654321', 'juan.perez@example.com', 'activo', 'vendedor'),
        ('E002', 'Ana', 'López', 'femenino', '1990-07-22', 'CE', '87654321', 'ana_lopez.png', 'Calle Secundaria 456', '912345678', 'ana.lopez@example.com', 'activo', 'cajero'),
        ('E003', 'Carlos', 'Martínez', 'masculino', '1982-11-30', 'pasaporte', 'A1234567', 'carlos_martinez.png', 'Av. Libertad 789', '987654321', 'carlos.martinez@example.com', 'no activo', 'administrador'),
        ('E004', 'Laura', 'Gómez', 'femenino', '1995-02-10', 'DNI', '23456789', 'laura_gomez.jpg', 'Calle Tercera 101', '923456789', 'laura.gomez@example.com', 'activo', 'vendedor'),
        ('E005', 'Jorge', 'Ramírez', 'masculino', '1988-06-25', 'CE', '34567890', 'jorge_ramirez.jpg', 'Av. Norte 202', '934567890', 'jorge.ramirez@example.com', 'activo', 'cajero');

    -- Insertar cinco clientes en la tabla clientes
    INSERT INTO clientes (id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email)
    VALUES
        ('C001', 'Pedro', 'Sánchez', 'masculino', 'DNI', '11223344', 'Av. Siempre Viva 742', '987654322', 'pedro.sanchez@example.com'),
        ('C002', 'María', 'García', 'femenino', 'CE', '22334455', 'Calle Falsa 123', '912345679', 'maria.garcia@example.com'),
        ('C003', 'Luis', 'Fernández', 'masculino', 'pasaporte', 'B1234568', 'Calle Real 456', '987654322', 'luis.fernandez@example.com'),
        ('C004', 'Elena', 'Martín', 'femenino', 'DNI', '33445566', 'Calle Sol 789', '923456780', 'elena.martin@example.com'),
        ('C005', 'Miguel', 'Torres', 'masculino', 'CE', '44556677', 'Av. Estrella 101', '934567891', 'miguel.torres@example.com');

    -- Insertar proveedores en la tabla proveedores
    INSERT INTO proveedores (id, razon_social, ruc, direccion, telefono, email, url)
    VALUES
        ('PR001', 'Proveedor ABC S.A.', '12345678901', 'Av. Comercio 456', '987654321', 'contacto@proveedorabc.com', 'http://www.proveedorabc.com'),
        ('PR002', 'Distribuidora XYZ', '10987654321', 'Calle de la Industria 789', '912345678', 'info@distribuidoraxyz.com', 'http://www.distribuidoraxyz.com'),
        ('PR003', 'Servicios Globales', '11223344556', 'Avenida Principal 101', '923456789', 'servicios@globales.com', 'http://www.serviciosglobales.com');

    INSERT INTO categorias (id, nombre_categoria, descripcion)
    VALUES
        ('CA001', 'Laptop', 'Productos electrónicos como computadoras, teléfonos y accesorios.'),
        ('CA002', 'Smartphone', 'Electrodomésticos para el hogar, como neveras y lavadoras.'),
        ('CA003', 'Impresora', 'Muebles para el hogar y oficina, incluyendo sillas, mesas y sofás.'),
        ('CA004', 'Pantalla', 'Muebles para el hogar y oficina, incluyendo sillas, mesas y sofás.');

    INSERT INTO compras (id, tipo_comprobante, nro_comprobante, fecha_emision, proveedor_id)
    VALUES
        ('CO001', 'factura', 'F001', '2024-07-15', 'PR001'),
        ('CO002', 'boleta', 'B001', '2024-07-16', 'PR002'),
        ('CO003', 'factura', 'F002', '2024-07-17', 'PR003'),
        ('CO004', 'factura', 'F003', '2024-07-17', 'PR003');
    -- Insertar cinco productos en la tabla productos
    INSERT INTO productos (id, descripcion, foto, marca, modelo, stock_inicial, stock_actual, categoria_id, proveedor_id, precio, compra_id)
    VALUES
        ('P001', 'Monitor Dell', 'default.png','Dell', 'Inspiron 15', 50, 50, 'CA004', 'PR001', 700, "CO001"),
        ('P002', 'Celular Huawei Pura 70 Pro','default.png', 'Huawei', 'Pura 70 pro', 100, 100, 'CA002', 'PR001', 900, "CO002"),
        ('P003', 'Canon Maprix 41100','default.png', 'Apple', 'iPad Pro', 30, 30, 'CA003', 'PR002', 800, "CO002"),
        ('P004', 'Monitor LG','default.png', 'LG', 'UltraWide', 40, 40, 'CA001', 'PR002', 300, "CO003"),
        ('P005', 'Impresora de tinta','default.png', 'HP', 'Maprix 51010', 20, 20, 'CA003', 'PR003', 200, "CO001"),
        ('P006', 'Impresora Laser','default.png', 'HP', 'LaserJet', 20, 20, 'CA002', 'PR003', 200, "CO004");

    -- Insertar tres ordenes de venta en la tabla orden_venta
    INSERT INTO orden_venta (id, empleado_id, fecha)
    VALUES
        ('OV001', 'E001', '2023-07-19'),
        ('OV002', 'E002', '2023-07-20'),
        ('OV003', 'E003', '2023-07-21');

    -- Insertar detalles de orden en la tabla detalle_orden
    INSERT INTO detalle_orden (id, id_orden, id_producto, cantidad, precio_unitario)
    VALUES
        ('DO001', 'OV001', 'P001', 2, 700),
        ('DO002', 'OV001', 'P002', 1, 900),
        ('DO003', 'OV002', 'P003', 1, 800),
        ('DO004', 'OV002', 'P004', 2, 300),
        ('DO005', 'OV003', 'P005', 1, 200);

    -- INSERTAR
    INSERT INTO ventas (id, tipo_comprobante, nro_comprobante, fecha_emision, proveedor_id, cliente_id)
    VALUES ('V001', 'factura', '001', '2024-07-20', 'PR001', 'C001');

    INSERT INTO detalle_venta (id, venta_id, producto_id, cantidad, precio_unitario)
    VALUES
        ('DV001', 'V001', 'P001', 2, 700),
        ('DV002', 'V001', 'P002', 1, 900);
    SQL;

    if ($conn->multi_query($sql) === TRUE) {
        //echo "Initial data inserted successfully<br>";

        // Ensure all results are processed
        while ($conn->next_result()) {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        }
    } else {
        //echo "Error inserting initial data: " . $conn->error . "<br>";
    }
} else {
    //echo "Database already exists.<br>";
}

// Close connection
$conn->close();
?>
