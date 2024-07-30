<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Inicializar variables para el reporte
$compras_diarias_result = null;
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

// Consulta para obtener el reporte de compras diarias
$compras_diarias_query = "
    SELECT
        c.fecha_emision AS fecha,
        COUNT(p.id) AS cantidad_compras,
        SUM(p.stock_inicial * p.precio) AS total_compras,
        SUM(p.stock_inicial) AS cantidad_productos_comprados
    FROM compras c
    JOIN productos p ON c.id = p.compra_id
    WHERE c.fecha_emision BETWEEN ? AND ?
    GROUP BY c.fecha_emision
    ORDER BY c.fecha_emision;
";

// Preparar y ejecutar la consulta
if ($stmt = $conn->prepare($compras_diarias_query)) {
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $compras_diarias_result = $stmt->get_result();
} else {
    die("Error en la consulta de compras diarias: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Compras Diarias - Sistema de Compra y Venta</title>
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
        <h2 class="text-xl font-bold mb-4">Compras Diarias</h2>
        
        <!-- Formulario para seleccionar fechas -->
<div class="flex justify-center">
        <form method="POST" class="mb-4">
            <div class="flex space-x-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-xl mt-3 hover:bg-slate-900/90">Generar Reporte</button>
                </div>
            </div>
        </form>
            </div>

        <div class="bg-white shadow-md rounded-lg p-4">
            <table class="w-full mt-4 border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Fecha</th>
                        <th class="border border-gray-300 px-4 py-2">Cantidad de Compras</th>
                        <th class="border border-gray-300 px-4 py-2">Productos Comprados</th>
                        <th class="border border-gray-300 px-4 py-2">Total de Compras</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($compras_diarias_result && $compras_diarias_result->num_rows > 0) : ?>
                        <?php while ($row = $compras_diarias_result->fetch_assoc()) : ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['fecha']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['cantidad_compras']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['cantidad_productos_comprados']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo number_format(htmlspecialchars($row['total_compras']), 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">No hay datos disponibles</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php $conn->close(); ?>
