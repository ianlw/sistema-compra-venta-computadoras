<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Consultar los clientes
$query = "SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email
          FROM clientes";
$result = $conn->query($query);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema de Compra y Venta</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu archivo CSS si tienes uno -->
</head>
<body>
    <header>
        <h1>Clientes</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="login.html">Iniciar Sesión</a></li>
                <li><a href="clientes.php">Clientes</a></li>
                <!-- Agregar más enlaces si es necesario -->
                <?php if ($tipo_empleado == 'administrador' || $tipo_empleado == 'cajero') : ?>
                    <li><a href="registrar_cliente.php">Agregar Cliente</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Lista de Clientes</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Sexo</th>
                    <th>Tipo de Documento</th>
                    <th>Número de Documento</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_documento']); ?></td>
                        <td><?php echo htmlspecialchars($row['numero_documento']); ?></td>
                        <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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

