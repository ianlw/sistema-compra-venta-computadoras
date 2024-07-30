<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../db.php'; // Incluye la conexión a la base de datos



// Verificar si se ha pasado un ID de venta
if (!isset($_GET['venta_id'])) {
    echo "<p class='text-red-500'>ID de venta no especificado.</p>";
    exit();
}

$venta_id = $_GET['venta_id'];

// Obtener la venta
$stmt = $conn->prepare("
    SELECT v.id, v.tipo_comprobante, v.nro_comprobante, v.fecha_emision, c.nombres AS cliente, c.apellidos AS cliente_apellidos
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    WHERE v.id = ?
");
$stmt->bind_param("s", $venta_id);
$stmt->execute();
$venta_result = $stmt->get_result();
$venta = $venta_result->fetch_assoc();
$stmt->close();

if (!$venta) {
    echo "<p class='text-red-500'>Venta no encontrada.</p>";
    exit();
}

// Obtener los detalles de la venta
$stmt = $conn->prepare("
    SELECT d.id, d.cantidad, d.precio_unitario, p.descripcion, p.marca, p.modelo
    FROM detalle_venta d
    JOIN productos p ON d.producto_id = p.id
    WHERE d.venta_id = ?
");
$stmt->bind_param("s", $venta_id);
$stmt->execute();
$detalle_result = $stmt->get_result();
$detalles = $detalle_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold ">Productos</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                    <li><a href="ventas.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Ventas</a></li>
            </ul>
        </nav>
        </div>
    </header>
    <div class="container mx-auto p-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4">Detalles de Venta</h1>

            <div class="mb-4">
                <h2 class="text-xl font-semibold">Información de la Venta</h2>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($venta['id']); ?></p>
                <p><strong>Tipo de Comprobante:</strong> <?php echo htmlspecialchars($venta['tipo_comprobante']); ?></p>
                <p><strong>Número de Comprobante:</strong> <?php echo htmlspecialchars($venta['nro_comprobante']); ?></p>
                <p><strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars($venta['fecha_emision']); ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente']) . ' ' . htmlspecialchars($venta['cliente_apellidos']); ?></p>
            </div>

            <h2 class="text-xl font-semibold mb-2">Detalles de los Productos</h2>

            <?php if ($detalles): ?>
                <table class="min-w-full bg-white border border-gray-300 mt-4">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Descripción</th>
                            <th class="py-2 px-4 border-b">Marca</th>
                            <th class="py-2 px-4 border-b">Modelo</th>
                            <th class="py-2 px-4 border-b">Cantidad</th>
                            <th class="py-2 px-4 border-b">Precio Unitario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($detalle['descripcion']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($detalle['marca']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($detalle['modelo']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($detalle['precio_unitario']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-red-500">No hay detalles para esta venta.</p>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="ventas.php" class="text-blue-500 hover:underline">Volver a la lista de ventas</a>
            </div>
        </div>
    </div>
</body>
</html>


