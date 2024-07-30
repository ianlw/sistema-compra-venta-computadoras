<?php
session_start();
include '../db.php'; // Incluye la conexión a la base de datos

$search_query = "";
$ordenes = [];

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $conn->prepare("
        SELECT ov.id AS orden_id, ov.fecha, e.nombres AS empleado_nombre
        FROM orden_venta ov
        JOIN empleados e ON ov.empleado_id = e.id
        WHERE ov.id LIKE ? OR e.nombres LIKE ? OR ov.fecha LIKE ?
    ");
    $search_term = "%".$search_query."%";
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
} else {
    $stmt = $conn->prepare("
        SELECT ov.id AS orden_id, ov.fecha, e.nombres AS empleado_nombre
        FROM orden_venta ov
        JOIN empleados e ON ov.empleado_id = e.id
    ");
}

$stmt->execute();
$result = $stmt->get_result();
$ordenes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Consultar detalles de cada orden
foreach ($ordenes as &$orden) {
    $stmt = $conn->prepare("
        SELECT p.descripcion, do.cantidad, do.precio_unitario
        FROM detalle_orden do
        JOIN productos p ON do.id_producto = p.id
        WHERE do.id_orden = ?
    ");
    $stmt->bind_param("s", $orden['orden_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $orden['detalles'] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja - Órdenes de Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl">Buscar Productos</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="./../clientes/clientes.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Gestionar Clientes</a></li>
            </ul>
        </nav>
        </div>
    </header>

    <main class="container mx-auto p-4 flex-grow">
        <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Todas las Órdenes de Venta</h2>

<div class="flex justify-center">
            <form method="get" action="caja.php" class="mb-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Buscar por ID, empleado o fecha" class="w-80 p-2 border border-gray-300 rounded-xl">
                <button type="submit" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
            </form>
        </div>

            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Fecha</th>
                        <th class="py-2 px-4 border-b">Empleado</th>
                        <th class="py-2 px-4 border-b">Detalles</th>
                        <th class="py-2 px-4 border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($orden['orden_id']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($orden['fecha']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($orden['empleado_nombre']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <ul>
                                    <?php foreach ($orden['detalles'] as $detalle): ?>
                                        <li>
                                            <?php echo htmlspecialchars($detalle['descripcion']); ?> - Cantidad: <?php echo htmlspecialchars($detalle['cantidad']); ?> - Precio Unitario: $<?php echo htmlspecialchars($detalle['precio_unitario']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <form action="procesar_venta.php" method="POST">
                                    <input type="hidden" name="orden_id" value="<?php echo htmlspecialchars($orden['orden_id']); ?>">
                                    <button type="submit" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Procesar Venta</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>
