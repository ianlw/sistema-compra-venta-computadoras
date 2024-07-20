<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Consultar la información del empleado
$stmt = $conn->prepare("
    SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email, foto
    FROM empleados 
    WHERE id = ?
");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $nombres, $apellidos, $sexo, $tipo_documento, $numero_documento, $direccion, $telefono, $email, $foto);
$stmt->fetch();
$stmt->close();
$conn->close();
$picture = "./img/empleados/" . $foto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Compra y Venta</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?></h1>
    
    <nav>
        <ul>
            <?php if ($tipo_empleado == 'vendedor') : ?>
                <li><a href="ventas.php">Gestionar Ventas</a></li>
                <li><a href="productos.php">Ver Productos</a></li>
            <?php elseif ($tipo_empleado == 'cajero') : ?>
                <li><a href="caja.php">Caja</a></li>
                <li><a href="reportes.php">Ver Reportes</a></li>
            <?php elseif ($tipo_empleado == 'administrador') : ?>
                <li><a href="empleados.php">Gestionar Empleados</a></li>
                <li><a href="proveedores.php">Gestionar Proveedores</a></li>
                <li><a href="productos.php">Gestionar Productos</a></li>
                <li><a href="reportes.php">Ver Reportes</a></li>
            <?php endif; ?>
            <li><a href="change_password.html">Cambiar Contraseña</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    
    <section>
        <h2>Información del Empleado</h2>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($id); ?></p>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombres); ?></p>
        <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($apellidos); ?></p>
        <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($tipo_documento); ?></p>
        <p><strong>Número de Documento:</strong> <?php echo htmlspecialchars($numero_documento); ?></p>
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        
        <?php if (!empty($foto)) : ?>
            <p><strong>Foto:</strong></p>
            <img src="<?php echo $picture; ?>" alt="Foto del empleado" style="width:150px; height:auto;">
        <?php endif; ?>

        <?php if ($tipo_empleado == 'vendedor') : ?>
            <h2>Panel de Vendedor</h2>
            <p>Contenido exclusivo para vendedores.</p>
        <?php elseif ($tipo_empleado == 'cajero') : ?>
            <h2>Panel de Cajero</h2>
            <p>Contenido exclusivo para cajeros.</p>
        <?php elseif ($tipo_empleado == 'administrador') : ?>
            <h2>Panel de Administrador</h2>
            <p>Contenido exclusivo para administradores.</p>
        <?php endif; ?>
    </section>
</body>
</html>
