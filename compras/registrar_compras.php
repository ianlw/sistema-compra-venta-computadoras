<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tipo_comprobante'], $_POST['nro_comprobante'], $_POST['fecha_emision'], $_POST['proveedor_id'], $_POST['producto_id'], $_POST['cantidad'], $_POST['precio'])) {
        $tipo_comprobante = $_POST['tipo_comprobante'];
        $nro_comprobante = $_POST['nro_comprobante'];
        $fecha_emision = $_POST['fecha_emision'];
        $proveedor_id = $_POST['proveedor_id'];
        $producto_ids = $_POST['producto_id'];
        $cantidades = $_POST['cantidad'];
        $precios = $_POST['precio'];

        $conn->begin_transaction();

        try {
            // Insertar en la tabla compras
            $stmt = $conn->prepare("INSERT INTO compras (tipo_comprobante, nro_comprobante, fecha_emision, proveedor_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $tipo_comprobante, $nro_comprobante, $fecha_emision, $proveedor_id);
            $stmt->execute();
            $compra_id = $stmt->insert_id;

            // Insertar en la tabla detalles_compra y actualizar stock de productos
            $stmt_detalle = $conn->prepare("INSERT INTO detalles_compra (compra_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
            $stmt_detalle->bind_param("iiid", $compra_id, $producto_id, $cantidad, $precio);

            $stmt_update_stock = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
            $stmt_update_stock->bind_param("ii", $cantidad, $producto_id);

            for ($i = 0; $i < count($producto_ids); $i++) {
                $producto_id = $producto_ids[$i];
                $cantidad = $cantidades[$i];
                $precio = $precios[$i];

                $stmt_detalle->execute();
                $stmt_update_stock->execute();
            }

            $conn->commit();
            echo "Compra registrada con éxito.";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error al registrar la compra: " . $e->getMessage();
        }
    } else {
        echo "Todos los campos son obligatorios.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Compra</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h2>Registrar Compra</h2>
        <form action="registrar_compras.php" method="post">
            <label for="tipo_comprobante">Tipo de Comprobante:</label>
            <input type="text" id="tipo_comprobante" name="tipo_comprobante" required>

            <label for="nro_comprobante">Número de Comprobante:</label>
            <input type="text" id="nro_comprobante" name="nro_comprobante" required>

            <label for="fecha_emision">Fecha de Emisión:</label>
            <input type="date" id="fecha_emision" name="fecha_emision" required>

            <label for="proveedor_id">Proveedor:</label>
            <select id="proveedor_id" name="proveedor_id" required>
                <?php
                $result = $conn->query("SELECT id, razon_social FROM proveedores");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['razon_social']}</option>";
                }
                ?>
            </select>

            <h3>Detalles de la Compra</h3>
            <div id="detalles">
                <div class="detalle">
                    <label for="producto_id[]">Producto:</label>
                    <select name="producto_id[]" required>
                        <?php
                        $result = $conn->query("SELECT id, descripcion FROM productos");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['descripcion']}</option>";
                        }
                        ?>
                    </select>

                    <label for="cantidad[]">Cantidad:</label>
                    <input type="number" name="cantidad[]" required>

                    <label for="precio[]">Precio:</label>
                    <input type="number" name="precio[]" step="0.01" required>
                </div>
            </div>
            <button type="button" onclick="agregarDetalle()">Agregar Detalle</button>
            <button type="submit">Registrar Compra</button>
        </form>
    </main>
    <script>
        function agregarDetalle() {
            const detalles = document.getElementById('detalles');
            const detalle = document.createElement('div');
            detalle.classList.add('detalle');
            detalle.innerHTML = `
                <label for="producto_id[]">Producto:</label>
                <select name="producto_id[]" required>
                    <?php
                    $result = $conn->query("SELECT id, descripcion FROM productos");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['descripcion']}</option>";
                    }
                    ?>
                </select>

                <label for="cantidad[]">Cantidad:</label>
                <input type="number" name="cantidad[]" required>

                <label for="precio[]">Precio:</label>
                <input type="number" name="precio[]" step="0.01" required>
            `;
            detalles.appendChild(detalle);
        }
    </script>
</body>
</html>
