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
// Consulta para obtener las compras y sus detalles
$query = "
    SELECT c.id AS compra_id, c.tipo_comprobante, c.nro_comprobante, c.fecha_emision, p.razon_social AS proveedor_nombre,
           dc.producto_id, pr.descripcion AS producto_nombre, dc.cantidad, dc.precio
    FROM compras c
    JOIN proveedores p ON c.proveedor_id = p.id
    JOIN detalles_compra dc ON c.id = dc.compra_id
    JOIN productos pr ON dc.producto_id = pr.id
    ORDER BY c.fecha_emision DESC, c.id DESC
";
$result = $conn->query($query);
$compras = [];

while ($row = $result->fetch_assoc()) {
    $compras[$row['compra_id']]['info'] = [
        'tipo_comprobante' => $row['tipo_comprobante'],
        'nro_comprobante' => $row['nro_comprobante'],
        'fecha_emision' => $row['fecha_emision'],
        'proveedor_nombre' => $row['proveedor_nombre']
    ];
    $compras[$row['compra_id']]['detalles'][] = [
        'producto_id' => $row['producto_id'],
        'producto_nombre' => $row['producto_nombre'],
        'cantidad' => $row['cantidad'],
        'precio' => $row['precio']
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Compras - Sistema de Compra y Venta</title>
    <link rel="stylesheet" href="styles.css">

</head>
    <header>
        <h1>Proveedores</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="login.html">Iniciar Sesión</a></li>
                <li><a href="proveedores.php">Compras</a></li>
                <!-- Agregar más enlaces si es necesario -->
                <?php if ($tipo_empleado == 'administrador') : ?>
                    <li><a href="registrar_compras.php">Agregar Compra</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
<body>
    <main>
        <h2>Listado de Compras</h2>
        <?php if (empty($compras)): ?>
            <p>No se encontraron compras registradas.</p>
        <?php else: ?>
            <?php foreach ($compras as $compra_id => $compra): ?>
                <div class="compra">
                    <h3>Compra #<?= $compra_id ?></h3>
                    <p><strong>Tipo de Comprobante:</strong> <?= $compra['info']['tipo_comprobante'] ?></p>
                    <p><strong>Número de Comprobante:</strong> <?= $compra['info']['nro_comprobante'] ?></p>
                    <p><strong>Fecha de Emisión:</strong> <?= $compra['info']['fecha_emision'] ?></p>
                    <p><strong>Proveedor:</strong> <?= $compra['info']['proveedor_nombre'] ?></p>
                    <h4>Detalles de la Compra:</h4>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compra['detalles'] as $detalle): ?>
                                <tr>
                                    <td><?= $detalle['producto_nombre'] ?></td>
                                    <td><?= $detalle['cantidad'] ?></td>
                                    <td><?= $detalle['precio'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
