<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

include '../db.php'; // Incluye la conexión a la base de datos

// Generar el próximo ID de empleado
$query = "SELECT MAX(CAST(SUBSTRING(id, 2) AS UNSIGNED)) AS max_id FROM empleados";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$next_id = 'E' . str_pad(($row['max_id'] + 1), 3, '0', STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $sexo = $_POST['sexo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $foto = $_POST['foto']; // En una implementación real, deberías manejar la subida de archivos
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];
    $tipo = $_POST['tipo'];

    // Preparar la consulta para insertar el nuevo empleado
    $stmt = $conn->prepare("
        INSERT INTO empleados (id, nombres, apellidos, sexo, fecha_nacimiento, tipo_documento, numero_documento, foto, direccion, telefono, email, estado, tipo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Verifica si se ha preparado la declaración correctamente
    if (!$stmt) {
        die('Error en la preparación de la declaración: ' . $conn->error);
    }
    
    // Ligar los parámetros
    $stmt->bind_param(
        "sssssssssssss", 
        $next_id, 
        $nombres, 
        $apellidos, 
        $sexo, 
        $fecha_nacimiento, 
        $tipo_documento, 
        $numero_documento, 
        $foto, 
        $direccion, 
        $telefono, 
        $email, 
        $estado, 
        $tipo
    );

    // Ejecutar la consulta y verificar si se insertó correctamente
    if ($stmt->execute()) {
        echo "Empleado registrado exitosamente.";
    } else {
        echo "Error al registrar el empleado: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado - Sistema de Compra y Venta</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu archivo CSS si tienes uno -->
</head>
<body>
    <main>
        <h2>Formulario de Registro de Empleado</h2>
        <form action="registrar_empleado.php" method="POST" enctype="multipart/form-data">
            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" required><br>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required><br>

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select><br>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"><br>

            <label for="tipo_documento">Tipo de Documento:</label>
            <select id="tipo_documento" name="tipo_documento">
                <option value="DNI">DNI</option>
                <option value="CE">CE</option>
                <option value="pasaporte">Pasaporte</option>
            </select><br>

            <label for="numero_documento">Número de Documento:</label>
            <input type="text" id="numero_documento" name="numero_documento" required><br>

            <label for="foto">Foto:</label>
            <input type="text" id="foto" name="foto" required><br>
            <!-- <input type="file" id="foto" name="foto"><br> -->

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required><br>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" pattern="[0-9]{9}" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>
                <option value="activo">Activo</option>
                <option value="no activo">No Activo</option>
            </select><br>

            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo" required>
                <option value="vendedor">Vendedor</option>
                <option value="cajero">Cajero</option>
                <option value="administrador">Administrador</option>
            </select><br>

            <button type="submit">Registrar Empleado</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

