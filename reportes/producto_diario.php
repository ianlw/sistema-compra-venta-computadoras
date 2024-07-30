<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Inicializar variables para el reporte
$search_query = '';
$reporte_kardex_result = null;
$producto_info = null;
$productos = [];

// Manejo del formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_query = trim($_POST['search_query']);

    // Consulta para obtener el ID del producto basado en la búsqueda
    $producto_query = "SELECT id, descripcion, marca, modelo FROM productos WHERE id LIKE '%$search_query%' OR descripcion LIKE '%$search_query%' OR marca LIKE '%$search_query%' OR modelo LIKE '%$search_query%'";
    $producto_result = $conn->query($producto_query);
    if ($producto_result && $producto_result->num_rows > 0) {
        // Obtener el primer producto encontrado
        $producto_info = $producto_result->fetch_assoc();
        $producto_id = $producto_info['id'];

        // Consulta para generar el reporte Kardex
        $reporte_kardex_query = "
            WITH Movimientos AS (
                SELECT
                    DATE(o.fecha) AS fecha,
                    COALESCE(SUM(d.cantidad), 0) AS cantidad_vendida,
                    COALESCE(SUM(d.precio_unitario * d.cantidad), 0) AS ventas_totales,
                    0 AS cantidad_comprada,
                    0 AS compras_totales,
                    GROUP_CONCAT(DISTINCT CONCAT(e.nombres, ' ', e.apellidos) ORDER BY e.nombres, e.apellidos SEPARATOR ', ') AS vendedores
                FROM productos p
                LEFT JOIN detalle_orden d ON p.id = d.id_producto
                LEFT JOIN orden_venta o ON d.id_orden = o.id
                LEFT JOIN empleados e ON o.empleado_id = e.id
                WHERE p.id = '$producto_id'
                GROUP BY fecha

                UNION ALL

                SELECT
                    DATE(c.fecha_emision) AS fecha,
                    0 AS cantidad_vendida,
                    0 AS ventas_totales,
                    COALESCE(SUM(d.cantidad), 0) AS cantidad_comprada,
                    COALESCE(SUM(d.precio_unitario * d.cantidad), 0) AS compras_totales,
                    NULL AS vendedores
                FROM productos p
                LEFT JOIN compras c ON p.compra_id = c.id
                LEFT JOIN detalle_orden d ON p.id = d.id_producto
                WHERE p.id = '$producto_id'
                GROUP BY fecha
            ),
            Resumen AS (
                SELECT
                    fecha,
                    SUM(cantidad_vendida) AS cantidad_vendida,
                    SUM(ventas_totales) AS ventas_totales,
                    SUM(cantidad_comprada) AS cantidad_comprada,
                    SUM(compras_totales) AS compras_totales,
                    GROUP_CONCAT(DISTINCT vendedores ORDER BY fecha SEPARATOR ', ') AS vendedores
                FROM Movimientos
                GROUP BY fecha
            )
            SELECT
                r.fecha AS fecha_movimiento,
                r.cantidad_vendida,
                r.ventas_totales,
                r.cantidad_comprada,
                r.compras_totales,
                (p.stock_inicial + COALESCE(SUM(r.cantidad_comprada), 0) - COALESCE(SUM(r.cantidad_vendida), 0)) AS stock_final,
                r.vendedores
            FROM Resumen r
            JOIN productos p ON p.id = '$producto_id'
            GROUP BY r.fecha, p.stock_inicial
            ORDER BY r.fecha;
        ";

        // Ejecutar la consulta
        if ($conn->multi_query($reporte_kardex_query)) {
            do {
                if ($result = $conn->store_result()) {
                    $reporte_kardex_result = $result;
                }
            } while ($conn->more_results() && $conn->next_result());
        } else {
            die("Error en la consulta del reporte Kardex: " . $conn->error);
        }
    }
} else {
    // Consulta para obtener todos los productos si no hay búsqueda
    $productos_query = "SELECT id, descripcion, marca, modelo FROM productos";
    $productos_result = $conn->query($productos_query);
    if ($productos_result && $productos_result->num_rows > 0) {
        $productos = $productos_result->fetch_all(MYSQLI_ASSOC);
    }
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
        <h2 class="text-xl font-bold mb-4">Buscar Producto</h2>
        
        <!-- Formulario de búsqueda -->
<div class="flex justify-center">
        <form method="POST" action="" class="mb-6 flex space-x-4">
            <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="ID, Descripción, Marca, o Modelo" class="w-80 px-3 py-2 border border-gray-300 rounded-xl">
            <button type="submit" name="search" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
        </form>
        </div>

        <?php if ($search_query && !$producto_info) : ?>
            <p class="text-red-600">No se encontraron productos que coincidan con la búsqueda.</p>
        <?php endif; ?>

        <?php if (!$search_query || $producto_info) : ?>
            <?php if (empty($producto_info)) : ?>
                <h2 class="text-xl font-bold mt-8 mb-4">Todos los Productos</h2>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <table class="w-full mt-4 border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Descripción</th>
                                <th class="border border-gray-300 px-4 py-2">Marca</th>
                                <th class="border border-gray-300 px-4 py-2">Modelo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto) : ?>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($producto['id']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($producto['marca']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($producto['modelo']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($reporte_kardex_result && $producto_info) : ?>
                <h2 class="text-xl font-bold mt-8 mb-4">Reporte Kardex del Producto</h2>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-lg font-bold mb-2">Producto: <?php echo htmlspecialchars($producto_info['descripcion']); ?></h3>
                    <p class="text-gray-700">Marca: <?php echo htmlspecialchars($producto_info['marca']); ?></p>
                    <p class="text-gray-700">Modelo: <?php echo htmlspecialchars($producto_info['modelo']); ?></p>
                    
                    <table class="w-full mt-4 border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Fecha</th>
                                <th class="border border-gray-300 px-4 py-2">Cantidad Vendida</th>
                                <th class="border border-gray-300 px-4 py-2">Ventas Totales</th>
                                <th class="border border-gray-300 px-4 py-2">Cantidad Comprada</th>
                                <th class="border border-gray-300 px-4 py-2">Compras Totales</th>
                                <th class="border border-gray-300 px-4 py-2">Stock Final</th>
                                <th class="border border-gray-300 px-4 py-2">Vendedores</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $reporte_kardex_result->fetch_assoc()) : ?>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['fecha_movimiento']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['cantidad_vendida']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['ventas_totales']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['cantidad_comprada']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['compras_totales']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['stock_final']); ?></td>
                                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['vendedores']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
