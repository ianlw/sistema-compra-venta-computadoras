<?php
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
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu archivo CSS si tienes uno -->
</head>
<body>
    <header>
        <h1>Registrar Proveedor</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="login.html">Iniciar Sesión</a></li>
                <li><a href="proveedores.php">Proveedores</a></li>
                <li><a href="registrar_proveedor.php">Agregar Proveedor</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Agregar Nuevo Proveedor</h2>
        <?php if (isset($error)) : ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="registrar_proveedor.php" method="post">
            <label for="razon_social">Razón Social:</label>
            <input type="text" id="razon_social" name="razon_social" required>
            
            <label for="ruc">RUC:</label>
            <input type="text" id="ruc" name="ruc" required>
            
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <button type="submit">Registrar Proveedor</button>
        </form>
    </main>
    
    <footer>
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>

