<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['tipo_empleado'] != 'administrador') {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_categoria = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];

    // Preparar y ejecutar la consulta de inserción
    $stmt = $conn->prepare("INSERT INTO categorias (id, nombre_categoria, descripcion) VALUES (?, ?, ?)");
    $id = 'CA' . str_pad(mt_rand(1, 9999), 3, '0', STR_PAD_LEFT); // Generar un ID único
    $stmt->bind_param("sss", $id, $nombre_categoria, $descripcion);

    if ($stmt->execute()) {
        echo "<p>Categoría agregada exitosamente.</p>";
    } else {
        echo "<p>Error al agregar categoría: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría - Sistema de Compra y Venta</title>
</head>
<body>
    <header>
        <h1>Agregar Categoría</h1>
        <nav>
            <ul>
                <li><a href="productos.php">Volver a Productos</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Formulario para Agregar Categoría</h2>
        <form method="post" action="">
            <label for="nombre_categoria">Nombre de la Categoría:</label>
            <input type="text" id="nombre_categoria" name="nombre_categoria" required>
            <br>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" cols="50"></textarea>
            <br>
            <input type="submit" value="Agregar Categoría">
        </form>
    </main>
</body>
</html>
