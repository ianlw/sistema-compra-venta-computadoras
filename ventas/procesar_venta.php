<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../db.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orden_id'])) {
    $orden_id = $_POST['orden_id'];

    // Obtener detalles de la orden
    $stmt = $conn->prepare("
        SELECT do.id_producto, do.cantidad, do.precio_unitario, p.descripcion
        FROM detalle_orden do
        JOIN productos p ON do.id_producto = p.id
        WHERE do.id_orden = ?
    ");
    $stmt->bind_param("s", $orden_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $detalles_orden = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (!isset($_POST['procesar_venta'])) {
        // Obtener el último número de comprobante
        $result = $conn->query("SELECT MAX(nro_comprobante) AS max_nro_comprobante FROM ventas");
        $row = $result->fetch_assoc();
        $last_id = $row['max_nro_comprobante'];
        $new_nro_comprobante = str_pad((int)substr($last_id, 2) + 1, 3, '0', STR_PAD_LEFT);

        // Obtener la fecha actual
        $fecha_emision = date('Y-m-d');

        // Mostrar el formulario para ingresar los datos de la venta
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Procesar Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold ">Procesar venta</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                    <li><a href="../clientes/clientes.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Clientes</a></li>
                    <li><a href="ventas.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Ventas</a></li>
            </ul>
        </nav>
        </div>
    </header>

            <main class="container mx-auto p-4 flex-grow">
                <section class="bg-white p-6 rounded-lg shadow-md">
                    <form action="procesar_venta.php" method="POST">
                        <input type="hidden" name="orden_id" value="<?php echo htmlspecialchars($orden_id); ?>">
                        <div class="mb-4">
                            <label for="tipo_comprobante" class="block text-sm font-medium text-gray-700">Tipo de Comprobante</label>
                            <select name="tipo_comprobante" id="tipo_comprobante" class="w-full px-3 py-2 border border-gray-300 rounded">
                                <option value="factura">Factura</option>
                                <option value="boleta">Boleta</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="nro_comprobante" class="block text-sm font-medium text-gray-700">Número de Comprobante</label>
                            <input type="text" name="nro_comprobante" id="nro_comprobante" class="w-full px-3 py-2 border border-gray-300 rounded" value="<?php echo htmlspecialchars($new_nro_comprobante); ?>" readonly>
                        </div>
                        <div class="mb-4">
                            <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente ID</label>
                            <input type="text" name="cliente_id" id="cliente_id" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="fecha_emision" class="block text-sm font-medium text-gray-700">Fecha de Emisión</label>
                            <input type="date" name="fecha_emision" id="fecha_emision" class="w-full px-3 py-2 border border-gray-300 rounded" value="<?php echo htmlspecialchars($fecha_emision); ?>" readonly>
                        </div>
                        <div class="mb-4">
                            <h3 class="block text-sm font-medium text-gray-700">Productos en la Orden</h3>
                            <ul class="list-disc pl-5">
                                <?php foreach ($detalles_orden as $detalle) { ?>
                                    <li>
                                        <span>Producto: <?php echo htmlspecialchars($detalle['descripcion']); ?></span><br>
                                        <span>Cantidad: <?php echo htmlspecialchars($detalle['cantidad']); ?></span><br>
                                        <span>Precio Unitario: $<?php echo htmlspecialchars($detalle['precio_unitario']); ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <button type="submit" name="procesar_venta" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Procesar Venta</button>
                    </form>
                </section>
            </main>

            <footer class="bg-blue-600 text-white text-center p-4">
                <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
            </footer>
        </body>
        </html>

        <?php
    } else {
        // Procesar los datos del formulario y los detalles de la orden
        $conn->begin_transaction(); // Iniciar la transacción

        $result = $conn->query("SELECT MAX(id) AS max_id FROM ventas");
        $row = $result->fetch_assoc();
        $last_id = $row['max_id'];
        $venta_id = 'V' . str_pad((int)substr($last_id, 2) + 1, 3, '0', STR_PAD_LEFT);

        $tipo_comprobante = $_POST['tipo_comprobante'];
        $nro_comprobante = $_POST['nro_comprobante'];
        $fecha_emision = $_POST['fecha_emision'];
        $proveedor_id = NULL; // Asignar el ID del proveedor (puede ser modificado)
        $cliente_id = $_POST['cliente_id'];

        // Insertar la venta en la tabla ventas
        $stmt = $conn->prepare("
            INSERT INTO ventas (id, tipo_comprobante, nro_comprobante, fecha_emision, proveedor_id, cliente_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $venta_id, $tipo_comprobante, $nro_comprobante, $fecha_emision, $proveedor_id, $cliente_id);
        $stmt->execute();
        $stmt->close();

        // Insertar los detalles de la venta y actualizar el stock de los productos
        foreach ($detalles_orden as $detalle) {
            $result = $conn->query("SELECT MAX(id) AS max_id FROM detalle_venta");
            $row = $result->fetch_assoc();
            $last_id = $row['max_id'];
            $detalle_id = 'DV' . str_pad((int)substr($last_id, 2) + 1, 3, '0', STR_PAD_LEFT);
            $producto_id = $detalle['id_producto'];
            $cantidad = $detalle['cantidad'];
            $precio_unitario = $detalle['precio_unitario'];

            // Insertar el detalle de la venta
            $stmt = $conn->prepare("
                INSERT INTO detalle_venta (id, venta_id, producto_id, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $detalle_id, $venta_id, $producto_id, $cantidad, $precio_unitario);
            $stmt->execute();
            $stmt->close();

            // Actualizar el stock del producto
            $stmt = $conn->prepare("
                UPDATE productos
                SET stock_actual = stock_actual - ?
                WHERE id = ?
            ");
            $stmt->bind_param("is", $cantidad, $producto_id);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit(); // Confirmar la transacción

        // Redirigir a la página de boleta
        header("Location: boleta.php?venta_id=" . $venta_id);
        exit();
    }
} else {
    echo "No se ha seleccionado una orden válida.";
}
?>
