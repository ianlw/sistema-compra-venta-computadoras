<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../db.php'; // Incluye la conexión a la base de datos
// Procesar solicitud de eliminación
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Eliminar los detalles asociados
    $stmt = $conn->prepare("DELETE FROM detalle_venta WHERE venta_id = ?");
    $stmt->bind_param("s", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Eliminar la venta
    $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
    $stmt->bind_param("s", $delete_id);
    if ($stmt->execute()) {
        echo "<p class='text-green-500'>Venta eliminada con éxito.</p>";
    } else {
        echo "<p class='text-red-500'>Error al eliminar la venta.</p>";
    }
    $stmt->close();
}

// Obtener el término de búsqueda
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Consulta para obtener las ventas
$stmt = $conn->prepare("
    SELECT v.id, v.tipo_comprobante, v.nro_comprobante, v.fecha_emision, c.nombres AS cliente, c.apellidos AS cliente_apellidos
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    WHERE v.id LIKE ? OR v.tipo_comprobante LIKE ? OR CONCAT(c.nombres, ' ', c.apellidos) LIKE ?
");
$searchTerm = "%$buscar%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$ventas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador de Ventas</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-white text-center text-2xl">Visualizar ventas</h1>
        <nav class="mt-2">
            <ul class="flex justify-center space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="../clientes/clientes.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar clientes</a></li>
                <li><a href="../empleados/empleados.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar empleados</a></li>
                <li><a href="../productos/productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar Productos</a></li>
            </ul>
        </nav>
        </div>
    </header>
    <div class="container mx-auto p-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4">Visualizador de Ventas</h1>

<div class="flex justify-center">
            <form action="ventas.php" method="get" class="mb-4">
                <input type="text" name="buscar" placeholder="Buscar por ID, tipo, o cliente" value="<?php echo htmlspecialchars($buscar); ?>" class="border rounded p-2 w-80 ">
                <button type="submit" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
            </form>
        </div>

            <?php if ($ventas): ?>
                <table class="min-w-full bg-white border border-gray-300 mt-4">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Tipo de Comprobante</th>
                            <th class="py-2 px-4 border-b">Número de Comprobante</th>
                            <th class="py-2 px-4 border-b">Fecha de Emisión</th>
                            <th class="py-2 px-4 border-b">Cliente</th>
                            <th class="py-2 px-4 border-b">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($venta['id']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($venta['tipo_comprobante']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($venta['nro_comprobante']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($venta['fecha_emision']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($venta['cliente']) . ' ' . htmlspecialchars($venta['cliente_apellidos']); ?></td>
                                <td class="py-2 px-4 border-b">
                                    <a href="detalle_venta.php?venta_id=<?php echo htmlspecialchars($venta['id']); ?>" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Ver Detalles</a>
                                    <form action="ventas.php" method="post" class="inline-block">
                                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($venta['id']); ?>">
                                        <button type="submit" class="ml-4 bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-red-500">No se encontraron ventas que coincidan con el término de búsqueda.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="../dashboard.php" class="text-blue-500 hover:underline">Volver al Dashboard</a>
    </div>
</body>
</html>
