<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];
// Obtener el término de búsqueda si existe
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta SQL
$sql = "
SELECT 
    c.id AS compra_id,
    c.tipo_comprobante,
    c.nro_comprobante,
    c.fecha_emision,
    p.id AS producto_id,
    p.descripcion AS producto_descripcion,
    p.marca,
    p.modelo,
    p.precio AS producto_precio,
    pr.razon_social AS proveedor_nombre,
    pr.ruc AS proveedor_ruc
FROM 
    compras c
JOIN 
    productos p ON c.id = p.compra_id
JOIN 
    proveedores pr ON c.proveedor_id = pr.id
";

// Si hay un término de búsqueda, agregar cláusula WHERE
if ($searchTerm !== '') {
    $sql .= " WHERE 
                compra_id LIKE '%$searchTerm%' OR 
                c.id LIKE '%$searchTerm%' OR 
                c.tipo_comprobante LIKE '%$searchTerm%' OR 
                c.nro_comprobante LIKE '%$searchTerm%' OR 
                c.fecha_emision LIKE '%$searchTerm%' OR 
                p.descripcion LIKE '%$searchTerm%' OR 
                p.marca LIKE '%$searchTerm%' OR 
                p.modelo LIKE '%$searchTerm%' OR 
                pr.razon_social LIKE '%$searchTerm%' OR 
                pr.ruc LIKE '%$searchTerm%'";
}

$sql .= " ORDER BY c.id, p.id";

// Ejecutar la consulta
$result = $conn->query($sql);

// Verificar si se obtuvieron resultados
$compras = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $compra_id = $row['compra_id'];
        if (!isset($compras[$compra_id])) {
            $compras[$compra_id] = [
                'info' => [
                    'tipo_comprobante' => $row['tipo_comprobante'],
                    'nro_comprobante' => $row['nro_comprobante'],
                    'fecha_emision' => $row['fecha_emision'],
                    'proveedor_nombre' => $row['proveedor_nombre'],
                ],
                'detalles' => [],
            ];
        }
        $compras[$compra_id]['detalles'][] = [
            'producto_nombre' => $row['producto_descripcion'],
            'cantidad' => 1, // Este es un valor predeterminado. Asegúrate de ajustar según tu estructura de datos real.
            'precio' => $row['producto_precio'],
        ];
    }
} else {
    $compras = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Compras - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mostrar notificación si hay mensajes de sesión
            const message = "<?php echo isset($_SESSION['message']) ? addslashes($_SESSION['message']) : ''; ?>";
            const error = "<?php echo isset($_SESSION['error']) ? addslashes($_SESSION['error']) : ''; ?>";

            if (message) {
                showNotification(message, 'bg-green-200', 'text-green-800');
                <?php unset($_SESSION['message']); ?>
            }

            if (error) {
                showNotification(error, 'bg-red-200', 'text-red-800');
                <?php unset($_SESSION['error']); ?>
            }

            function showNotification(message, bgColor, textColor) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 mb-4 rounded-lg ${bgColor} ${textColor} shadow-lg`;
                notification.innerHTML = message;
                document.body.appendChild(notification);

                // Auto hide after 5 seconds
                setTimeout(() => {
                    notification.classList.add('opacity-0');
                    setTimeout(() => notification.remove(), 300); // Wait for fade out to complete before removing
                }, 2000);
            }
        });
    </script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-white text-center text-2xl">Compras</h1>
        <nav class="mt-2">
            <ul class="flex justify-center space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Home</a></li>
                <li><a href="../proveedores/proveedores.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Listar proveedores</a></li>
                <?php if ($tipo_empleado == 'administrador') : ?>
                    <li><a href="registrar_compras.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Agregar Compra</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Listado de Compras</h2>
        
        <!-- Formulario de búsqueda -->
<div class="flex justify-center">
    <form method="GET" action="" class="mb-4">
        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Buscar compras..." class="px-4 py-2 border rounded-lg">
        <button type="submit" class="mt-2 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
    </form>
</div>

        <?php if (empty($compras)): ?>
            <p class="text-gray-700">No se encontraron compras registradas.</p>
        <?php else: ?>
            <?php foreach ($compras as $compra_id => $compra): ?>
                <div class="bg-white shadow-md rounded-lg p-6 mb-4">
                    <h3 class="text-lg font-semibold mb-2">Compra #<?= htmlspecialchars($compra_id) ?></h3>
                    <p class="text-gray-700"><strong>Tipo de Comprobante:</strong> <?= htmlspecialchars($compra['info']['tipo_comprobante']) ?></p>
                    <p class="text-gray-700"><strong>Número de Comprobante:</strong> <?= htmlspecialchars($compra['info']['nro_comprobante']) ?></p>
                    <p class="text-gray-700"><strong>Fecha de Emisión:</strong> <?= htmlspecialchars($compra['info']['fecha_emision']) ?></p>
                    <p class="text-gray-700"><strong>Proveedor:</strong> <?= htmlspecialchars($compra['info']['proveedor_nombre']) ?></p>
                    <h4 class="text-md font-semibold mt-4">Detalles de la Compra:</h4>
                    <table class="min-w-full bg-white mt-2">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Producto</th>
                                <th class="px-4 py-2">Cantidad</th>
                                <th class="px-4 py-2">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compra['detalles'] as $detalle): ?>
                                <tr>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($detalle['cantidad']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($detalle['precio']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Botón para eliminar la compra -->
                    <form method="POST" action="delete_compra.php" class="mt-4">
                        <input type="hidden" name="compra_id" value="<?= htmlspecialchars($compra_id) ?>">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer class=" text-slate-700 p-4 text-center">
        <p> No existen más compras</p>
    </footer>
</body>
</html>
