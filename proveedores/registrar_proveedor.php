<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Verificar si el usuario tiene permisos para acceder
if ($tipo_empleado != 'administrador' && $tipo_empleado != 'cajero') {
    header("Location: ../index.php");
    exit();
}

// Manejo del formulario de registro de proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $razon_social = $_POST['razon_social'];
    $ruc = $_POST['ruc'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    // Validación simple
    if (empty($razon_social) || empty($ruc) || empty($direccion) || empty($telefono) || empty($email)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Generar el nuevo ID automáticamente
        $query = "SELECT MAX(id) AS max_id FROM proveedores";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $max_id = $row['max_id'];
        $new_id = 'PR' . str_pad(substr($max_id, 2) + 1, 3, '0', STR_PAD_LEFT);

        // Insertar el nuevo proveedor en la base de datos
        $stmt = $conn->prepare("INSERT INTO proveedores (id, razon_social, ruc, direccion, telefono, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $new_id, $razon_social, $ruc, $direccion, $telefono, $email);

        if ($stmt->execute()) {
            header("Location: proveedores.php");
            exit();
        } else {
            $error = "Error al registrar el proveedor: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Proveedor - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Registrar Proveedor</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                    <li><a href="proveedores.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar Proveedores</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-6 bg-white rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-bold mb-4">Agregar Nuevo Proveedor</h2>
        <?php if (isset($error)) : ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="registrar_proveedor.php" method="post" class="space-y-4">
            <div>
                <label for="razon_social" class="block text-gray-700">Razón Social:</label>
                <input type="text" id="razon_social" name="razon_social" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="ruc" class="block text-gray-700">RUC:</label>
                <input type="text" id="ruc" name="ruc" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="direccion" class="block text-gray-700">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="telefono" class="block text-gray-700">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="email" class="block text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Registrar Proveedor</button>
        </form>
    </main>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
