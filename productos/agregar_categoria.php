<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['tipo_empleado'] != 'administrador') {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Inicializar mensajes
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = ['type' => '', 'text' => ''];
}

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_categoria = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];

    // Preparar y ejecutar la consulta de inserción
    $stmt = $conn->prepare("INSERT INTO categorias (id, nombre_categoria, descripcion) VALUES (?, ?, ?)");
    $id = 'CA' . str_pad(mt_rand(1, 9999), 3, '0', STR_PAD_LEFT); // Generar un ID único
    $stmt->bind_param("sss", $id, $nombre_categoria, $descripcion);

    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Categoría agregada exitosamente.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al agregar categoría: ' . $stmt->error];
    }
    $stmt->close();
    $conn->close();

    // Redirigir para evitar el reenvío del formulario en caso de refrescar la página
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Agregar Categoría</h1>
        <nav class="mt-2">
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar Productos</a></li>
            </ul>
        </nav>
</div>
    </header>
    
    <main class="p-6 max-w-4xl mx-auto">
        <h2 class="text-xl font-bold mb-4">Formulario para Agregar Categoría</h2>

        <!-- Mensaje de notificación -->
        <?php if ($_SESSION['message']['text']): ?>
            <div class="mb-4 p-4 rounded-lg text-white 
                <?= $_SESSION['message']['type'] == 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php $_SESSION['message'] = ['type' => '', 'text' => '']; // Limpiar mensaje ?>
        <?php endif; ?>

        <form method="post" action="" class="bg-white p-6 shadow-md rounded-lg">
            <div class="mb-4">
                <label for="nombre_categoria" class="block text-gray-700 font-medium">Nombre de la Categoría:</label>
                <input type="text" id="nombre_categoria" name="nombre_categoria" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 font-medium">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
            </div>
            <div>
                <input type="submit" value="Agregar Categoría" class="bg-slate-900 text-white px-4 py-2 rounded-xl mt-3 hover:bg-slate-900/90">
            </div>
        </form>
    </main>
</body>
</html>
