<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Consultar los productos
$query = "SELECT p.id, p.descripcion, p.marca, p.modelo, p.stock_actual, p.precio, c.nombre_categoria, pr.razon_social
          FROM productos p
          INNER JOIN categorias c ON p.categoria_id = c.id
          INNER JOIN proveedores pr ON p.proveedor_id = pr.id";
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
    <title>Productos - Sistema de Compra y Venta</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu archivo CSS si tienes uno -->
</head>
<body>
    <header>
        <h1>Productos</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="login.html">Iniciar Sesión</a></li>
                <li><a href="productos.php">Productos</a></li>
                <!-- Agregar más enlaces si es necesario -->
                <?php if ($tipo_empleado == 'administrador') : ?>
                    <li><a href="agregar_categoria.php">Agregar Categoría</a></li>
                    <li><a href="agregar_producto.php">Agregar Producto</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Lista de Productos</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Stock Actual</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($row['marca']); ?></td>
                        <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock_actual']); ?></td>
                        <td><?php echo htmlspecialchars($row['precio']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_categoria']); ?></td>
                        <td><?php echo htmlspecialchars($row['razon_social']); ?></td>
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
