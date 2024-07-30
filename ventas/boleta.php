<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../db.php'; // Incluye la conexión a la base de datos



$venta_id = isset($_GET['venta_id']) ? $_GET['venta_id'] : '';

if ($venta_id) {
    // Obtener detalles de la venta
    $stmt = $conn->prepare("
        SELECT v.tipo_comprobante, v.nro_comprobante, v.fecha_emision, c.nombres AS cliente, c.apellidos,  p.descripcion, dv.cantidad, dv.precio_unitario
        FROM ventas v
        JOIN detalle_venta dv ON v.id = dv.venta_id
        JOIN productos p ON dv.producto_id = p.id
        JOIN clientes c ON v.cliente_id = c.id
        WHERE v.id = ?
    ");
    $stmt->bind_param("s", $venta_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $venta = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if ($venta) {
        $tipo_comprobante = $venta[0]['tipo_comprobante'];
        $nro_comprobante = $venta[0]['nro_comprobante'];
        $fecha_emision = $venta[0]['fecha_emision'];
        $cliente = $venta[0]['cliente'] . $venta[0]['apellidos'];
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Boleta de Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 text-gray-900">
            <div class="container mx-auto p-4">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h1 class="text-2xl font-bold mb-4">Boleta de Venta</h1>
                    <p><strong>Tipo de Comprobante:</strong> <?php echo htmlspecialchars($tipo_comprobante); ?></p>
                    <p><strong>Número de Comprobante:</strong> <?php echo htmlspecialchars($nro_comprobante); ?></p>
                    <p><strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars($fecha_emision); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente); ?></p>

                    <table class="min-w-full bg-white border border-gray-300 mt-4">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Producto</th>
                                <th class="py-2 px-4 border-b">Cantidad</th>
                                <th class="py-2 px-4 border-b">Precio Unitario</th>
                                <th class="py-2 px-4 border-b">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_venta = 0;
                            foreach ($venta as $item) {
                                $total_item = $item['cantidad'] * $item['precio_unitario'];
                                $total_venta += $total_item;
                                ?>
                                <tr>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['descripcion']); ?></td>
                                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($item['cantidad']); ?></td>
                                    <td class="py-2 px-4 border-b">$<?php echo htmlspecialchars($item['precio_unitario']); ?></td>
                                    <td class="py-2 px-4 border-b">$<?php echo htmlspecialchars($total_item); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="py-2 px-4 border-t text-right font-bold">Total</td>
                                <td class="py-2 px-4 border-t font-bold">$<?php echo htmlspecialchars($total_venta); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
<div class="text-center mt-4">
    <a href="../dashboard.php" class="text-blue-500 hover:underline">Volver al Dashboard</a>
</div>

        </body>
        </html>

        <?php
    } else {
        echo "No se encontró la venta.";
    }
} else {
    echo "No se ha proporcionado un ID de venta válido.";
}
?>

