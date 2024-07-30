<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['tipo_empleado'] != 'administrador') {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $nro_comprobante = $_POST['nro_comprobante'];
    $fecha_emision = $_POST['fecha_emision'];
    $proveedor_id = $_POST['proveedor_id'];

    // Obtener el último ID y generar el siguiente ID
    $result = $conn->query("SELECT MAX(id) AS max_id FROM compras");
    $row = $result->fetch_assoc();
    $last_id = $row['max_id'];
    $new_id = 'CO' . str_pad((int)substr($last_id, 2) + 1, 3, '0', STR_PAD_LEFT);

    // Preparar y ejecutar la consulta de inserción
    $stmt = $conn->prepare("INSERT INTO compras (id, tipo_comprobante, nro_comprobante, fecha_emision, proveedor_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $new_id, $tipo_comprobante, $nro_comprobante, $fecha_emision, $proveedor_id);

    if ($stmt->execute()) {
//        echo "<p>Compra registrada exitosamente.</p>";
    } else {
//        echo "<p>Error al registrar compra: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Obtener proveedores
$proveedores = [];
$result = $conn->query("SELECT id, razon_social FROM proveedores");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Compra - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-white text-center text-2xl">Registrar Compra</h1>
        <nav class="mt-2">
            <ul class="flex justify-center space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Home</a></li>
                <li><a href="compras.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Listar compras</a></li>
                <li><a href="../proveedores/proveedores.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Listar proveedores</a></li>
                <li><a href="productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Volver a Productos</a></li>
            </ul>
        </nav>
</div>
    </header>
    
    <main class="container mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Formulario para Registrar Compra</h2>
        <form method="post" action="" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="tipo_comprobante" class="block text-gray-700 font-medium mb-2">Tipo de Comprobante:</label>
                <select id="tipo_comprobante" name="tipo_comprobante" required class="px-4 py-2 border rounded-lg w-full">
                    <option value="factura">Factura</option>
                    <option value="boleta">Boleta</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="nro_comprobante" class="block text-gray-700 font-medium mb-2">Número de Comprobante:</label>
                <input type="text" id="nro_comprobante" name="nro_comprobante" required class="px-4 py-2 border rounded-lg w-full">
            </div>
            <div class="mb-4">
                <label for="fecha_emision" class="block text-gray-700 font-medium mb-2">Fecha de Emisión:</label>
                <input type="date" id="fecha_emision" name="fecha_emision" required class="px-4 py-2 border rounded-lg w-full">
            </div>
            <div class="mb-4">
                <label for="proveedor_id" class="block text-gray-700 font-medium mb-2">Proveedor:</label>
                <select id="proveedor_id" name="proveedor_id" required class="px-4 py-2 border rounded-lg w-full">
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                            <?php echo htmlspecialchars($proveedor['razon_social']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-slate-900 m-4 text-white px-4 mb-4 py-2 rounded-xl hover:bg-slate-900/90">Registrar Compra</button>
            <a href="../productos/agregar_producto.php" class="bg-slate-900 text-white px-4 mb-4 py-2 rounded-xl hover:bg-slate-900/90">Registrar productos</a>
        </form>
    </main>
</body>
</html>

