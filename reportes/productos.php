<?ph
<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Inicializar variables para el reporte
$productos_result = null;

// Consulta para obtener el reporte de productos
$productos_query = "
    SELECT
        p.id AS producto_id,
        p.descripcion,
        p.foto,
        p.marca,
        p.modelo,
        p.stock_inicial,
        p.stock_actual,
        c.nombre_categoria AS categoria,
        pr.razon_social AS proveedor,
        p.precio,
        p.compra_id
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
    ORDER BY p.id;
";

// Ejecutar la consulta
if ($conn->multi_query($productos_query)) {
    do {
        if ($result = $conn->store_result()) {
            $productos_result = $result;
        }
    } while ($conn->more_results() && $conn->next_result());
} else {
    die("Error en la consulta de productos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Productos - Sistema de Compra y Venta</title>

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
        <h2 class="text-xl font-bold mb-4">Lista de Productos</h2>
        
        <div class="bg-white shadow-md rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full mt-4 border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Descripción</th>
                        <th class="border border-gray-300 px-4 py-2">Foto</th>
                        <th class="border border-gray-300 px-4 py-2">Marca</th>
                        <th class="border border-gray-300 px-4 py-2">Modelo</th>
                        <th class="border border-gray-300 px-4 py-2">Stock Inicial</th>
                        <th class="border border-gray-300 px-4 py-2">Stock Actual</th>
                        <th class="border border-gray-300 px-4 py-2">Categoría</th>
                        <th class="border border-gray-300 px-4 py-2">Proveedor</th>
                        <th class="border border-gray-300 px-4 py-2">Precio</th>
                        <th class="border border-gray-300 px-4 py-2">Compra ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($productos_result && $productos_result->num_rows > 0) : ?>
                        <?php while ($row = $productos_result->fetch_assoc()) : ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['producto_id']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <img src="../img/productos/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto del Producto" class="w-16 h-16 object-cover rounded-xl">
                                </td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['marca']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['modelo']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['stock_inicial']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['stock_actual']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['categoria']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['proveedor']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo number_format(htmlspecialchars($row['precio']), 2); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['compra_id']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="11" class="border border-gray-300 px-4 py-2 text-center">No hay productos disponibles</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php $conn->close(); ?>

