<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Incluir el archivo de configuración para obtener BASE_PATH
include '../config.php';
include BASE_PATH . '/db.php';

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
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];
    $tipo = $_POST['tipo'];

    // Procesar la foto
    $foto = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_path = '../img/empleados/' . $foto;

    if (move_uploaded_file($foto_tmp, $foto_path)) {
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
            echo "<div class='bg-green-500 text-white p-4 rounded'>Empleado registrado exitosamente.</div>";
        } else {
            echo "<div class='bg-red-500 text-white p-4 rounded'>Error al registrar el empleado: " . $stmt->error . "</div>";
        }

        // Cerrar la declaración y la conexión
        $stmt->close();
    } else {
        echo "<div class='bg-red-500 text-white p-4 rounded'>Error al subir la foto.</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Registrar Empleado</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                    <li><a href="empleados.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar Empleados</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-6 bg-white shadow-md rounded-lg mt-8">
        <h2 class="text-xl font-semibold mb-6">Formulario de Registro de Empleado</h2>
        <form action="registrar_empleado.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="nombres" class="block text-gray-700">Nombres:</label>
                <input type="text" id="nombres" name="nombres" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="apellidos" class="block text-gray-700">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="sexo" class="block text-gray-700">Sexo:</label>
                <select id="sexo" name="sexo" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                </select>
            </div>

            <div>
                <label for="fecha_nacimiento" class="block text-gray-700">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="tipo_documento" class="block text-gray-700">Tipo de Documento:</label>
                <select id="tipo_documento" name="tipo_documento" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
                    <option value="DNI">DNI</option>
                    <option value="CE">CE</option>
                    <option value="pasaporte">Pasaporte</option>
                </select>
            </div>

            <div>
                <label for="numero_documento" class="block text-gray-700">Número de Documento:</label>
                <input type="text" id="numero_documento" name="numero_documento" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="foto" class="block text-gray-700">Foto:</label>
                <input type="file" id="foto" name="foto" class="mt-1 block w-full p-2 border border-gray-300 rounded-lg" accept="image/*">
            </div>

            <div>
                <label for="direccion" class="block text-gray-700">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="telefono" class="block text-gray-700">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" pattern="[0-9]{9}" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="email" class="block text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="estado" class="block text-gray-700">Estado:</label>
                <select id="estado" name="estado" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
                    <option value="activo">Activo</option>
                    <option value="no activo">No Activo</option>
                </select>
            </div>

            <div>
                <label for="tipo" class="block text-gray-700">Tipo:</label>
                <select id="tipo" name="tipo" required class="mt-1 block w-full p-2 border border-gray-300 rounded-lg">
                    <option value="vendedor">Vendedor</option>
                    <option value="cajero">Cajero</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>

            <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-xl mt-3 hover:bg-slate-900/90">Registrar</button>
        </form>
    </main>
</body>
</html>
