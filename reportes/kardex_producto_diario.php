<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

if (isset($_POST['producto_id'])) {
    $producto_id = $_POST['producto_id'];

    // Consulta para obtener el historial de movimientos del producto
    $query = "
        SELECT 
            p.descripcion, 
            p.marca, 
            p.modelo, 
            p.stock_actual,
            COALESCE(c.fecha_emision, v.fecha_emision) AS fecha,
            COALESCE(d.cantidad, 0) AS cantidad,
            COALESCE(d.precio_unitario, 0) AS precio_unitario,
            CASE
                WHEN c.id IS NOT NULL THEN 'Compra'
                WHEN v.id IS NOT NULL THEN 'Venta'
            END AS tipo
        FROM productos p
        LEFT JOIN detalle_orden d ON p.id = d.id_producto
        LEFT JOIN orden_venta c ON d.id_orden = c.id
        LEFT JOIN ventas v ON d.id_producto = v.id
        WHERE p.id = '$producto_id'
        ORDER BY fecha;
    ";

    $result = $conn->query($query);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    // Obtener la información del producto
    $producto_info_query = "SELECT descripcion, marca, modelo FROM productos WHERE id = '$producto_id'";
    $producto_result = $conn->query($producto_info_query);
    $producto = $producto_result->fetch_assoc();

    $conn->close();
} else {
    die("Producto no especificado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Kardex - Sistema de Compra y Venta</title>

<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl">Reportes</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="producto_diario.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Producto Kardex</a></li>
                <li><a href="producto_dos_fechas.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Producto entre fechas</a></li>
                <li><a href="empleado.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Empleados</a></li>
                <li><a href="productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Productos</a></li>
                <li><a href="ventas_diarias.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Ventas diarias</a></li>
                <li><a href="ventas_dos_fechas.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Ventas entre fechas</a></li>
                <li><a href="compra_dos_fechas.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Compra entre fechas</a></li>
            </ul>
        </nav>
        </div>
    </header>

    
    <main class="p-4">
        <h2 class="text-xl font-bold mb-4">Reporte Kardex del Producto</h2>

        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-bold mb-2">Producto: <?php echo htmlspecialchars($producto['descripcion']); ?></h3>
            <p class="text-gray-700">Marca: <?php echo htmlspecialchars($producto['marca']); ?></p>
            <p class="text-gray-700">Modelo: <?php echo htmlspecialchars($producto['modelo']); ?></p>
        </div>

        <table class="min-w-full bg-white border border-gray-300 mt-4">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Fecha</th>
                    <th class="border px-4 py-2">Tipo</th>
                    <th class="border px-4 py-2">Cantidad</th>
                    <th class="border px-4 py-2">Precio Unitario</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['fecha']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['tipo']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td class="border px-4 py-2">$<?php echo htmlspecialchars($row['precio_unitario']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    
    <footer class="bg-blue-600 text-white p-4 text-center">
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
