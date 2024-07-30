<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Obtener el ID de la orden de la URL
$orden_id = $_GET['orden_id'];

// Consultar la información de la orden
$query_orden = "SELECT * FROM orden_venta WHERE id = '$orden_id'";
$result_orden = $conn->query($query_orden);
$orden = $result_orden->fetch_assoc();

// Consultar los detalles de la orden
$query_detalles = "SELECT do.id_producto, do.cantidad, do.precio_unitario, p.descripcion 
                   FROM detalle_orden do
                   INNER JOIN productos p ON do.id_producto = p.id
                   WHERE do.id_orden = '$orden_id'";
$result_detalles = $conn->query($query_detalles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Orden - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl">Confirmación de Orden</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="login.html" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Iniciar Sesión</a></li>
                <li><a href="productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Productos</a></li>
                <li><a href="carrito.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Carrito</a></li>
            </ul>
        </nav>
</div>
    </header>
    
    <main class="p-4">
        <h2 class="text-xl font-bold mb-4">Orden de Venta: <?php echo htmlspecialchars($orden['id']); ?></h2>
        <div class="bg-white shadow-md rounded-lg p-4">
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($orden['fecha']); ?></p>
            <h3 class="text-lg font-bold mb-2">Detalles de la Orden:</h3>
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="border-b p-2">Producto</th>
                        <th class="border-b p-2">Cantidad</th>
                        <th class="border-b p-2">Precio Unitario</th>
                        <th class="border-b p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($detalle = $result_detalles->fetch_assoc()) : ?>
                        <tr>
                            <td class="border-b p-2"><?php echo htmlspecialchars($detalle['descripcion']); ?></td>
                            <td class="border-b p-2"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                            <td class="border-b p-2">$<?php echo htmlspecialchars($detalle['precio_unitario']); ?></td>
                            <td class="border-b p-2">$<?php echo htmlspecialchars($detalle['cantidad'] * $detalle['precio_unitario']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <footer class="text-slate-700 p-4 text-center">
        <p>No existen más elementos en la orden de compra</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
