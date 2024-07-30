<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Inicializar variables
$search_query = '';
$empleado_info = null;
$empleados = [];

// Manejo del formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_query = trim($_POST['search_query']);

    // Consulta para obtener todos los empleados
    $empleados_query = "SELECT id, nombres, apellidos, sexo FROM empleados";
    $empleados_result = $conn->query($empleados_query);
    if ($empleados_result) {
        $empleados = $empleados_result->fetch_all(MYSQLI_ASSOC);
    }

    // Consulta para generar el reporte de empleados basado en la búsqueda
    $reporte_empleado_query = "
        SELECT 
            e.id AS 'Empleado ID',
            e.nombres AS 'Nombres',
            e.apellidos AS 'Apellidos',
            o.id AS 'Orden ID',
            o.fecha AS 'Fecha de Orden',
            p.id AS 'Producto ID',
            p.descripcion AS 'Descripción del Producto',
            d.cantidad AS 'Cantidad',
            d.precio_unitario AS 'Precio Unitario',
            (d.cantidad * d.precio_unitario) AS 'Total por Producto'
        FROM 
            empleados e
        JOIN 
            orden_venta o ON e.id = o.empleado_id
        JOIN 
            detalle_orden d ON o.id = d.id_orden
        JOIN 
            productos p ON d.id_producto = p.id
        WHERE 
            (e.nombres LIKE '%$search_query%' OR
            e.apellidos LIKE '%$search_query%' OR
            e.sexo LIKE '%$search_query%')
        ORDER BY o.id, p.id;
    ";

    // Ejecutar la consulta
    $reporte_empleado_result = $conn->query($reporte_empleado_query);
    if (!$reporte_empleado_result) {
        die("Error en la consulta del reporte de empleados: " . $conn->error);
    }

    // Obtener información del empleado
    if (!empty($search_query)) {
        $empleado_info_query = "SELECT id, nombres, apellidos FROM empleados WHERE nombres LIKE '%$search_query%' OR apellidos LIKE '%$search_query%' OR sexo LIKE '%$search_query%'";
        $empleado_info_result = $conn->query($empleado_info_query);
        if ($empleado_info_result) {
            $empleado_info = $empleado_info_result->fetch_assoc();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Empleados - Sistema de Compra y Venta</title>
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
        <h2 class="text-xl font-bold mb-4">Buscar Empleado</h2>
        
        <!-- Formulario de búsqueda -->
<div class="flex justify-center">
        <form method="POST" action="" class="mb-6">
            <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Buscar por nombre, apellido o sexo" class="w-80 px-3 py-2 border border-gray-300 rounded-xl">
            <button type="submit" name="search" class="bg-slate-900 text-white px-4 py-2 rounded-xl mt-3 hover:bg-slate-900/90">Buscar</button>
        </form>
        </div>

        <!-- Mostrar todos los empleados si no se ha realizado una búsqueda -->
        <?php if (empty($search_query)) : ?>
            <h2 class="text-xl font-bold mb-4">Lista de Empleados</h2>
            <div class="bg-white shadow-md rounded-lg p-4 mb-8">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">ID</th>
                            <th class="border border-gray-300 px-4 py-2">Nombres</th>
                            <th class="border border-gray-300 px-4 py-2">Apellidos</th>
                            <th class="border border-gray-300 px-4 py-2">Sexo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $empleado) : ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($empleado['id']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($empleado['nombres']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($empleado['apellidos']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($empleado['sexo']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Mostrar reporte del empleado encontrado -->
        <?php if ($reporte_empleado_result && $empleado_info) : ?>
            <h2 class="text-xl font-bold mt-8 mb-4">Reporte de Órdenes del Empleado</h2>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h3 class="text-lg font-bold mb-2">Empleado: <?php echo htmlspecialchars($empleado_info['nombres']) . ' ' . htmlspecialchars($empleado_info['apellidos']); ?></h3>
                
                <table class="w-full mt-4 border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Orden ID</th>
                            <th class="border border-gray-300 px-4 py-2">Fecha de Orden</th>
                            <th class="border border-gray-300 px-4 py-2">Producto ID</th>
                            <th class="border border-gray-300 px-4 py-2">Descripción del Producto</th>
                            <th class="border border-gray-300 px-4 py-2">Cantidad</th>
                            <th class="border border-gray-300 px-4 py-2">Precio Unitario</th>
                            <th class="border border-gray-300 px-4 py-2">Total por Producto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $reporte_empleado_result->fetch_assoc()) : ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Orden ID']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Fecha de Orden']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Producto ID']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Descripción del Producto']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Cantidad']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Precio Unitario']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['Total por Producto']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
