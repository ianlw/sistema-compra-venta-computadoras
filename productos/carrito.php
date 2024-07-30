<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Manejar la eliminación de un producto del carrito
if (isset($_POST['remove_product'])) {
    $product_to_remove = $_POST['product_id'];
    if (($key = array_search($product_to_remove, array_keys($_SESSION['carrito']))) !== false) {
        unset($_SESSION['carrito'][$product_to_remove]);
    }
}

// Manejar la actualización de la cantidad de un producto en el carrito
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    if ($quantity > 0) {
        $_SESSION['carrito'][$product_id] = $quantity;
    } else {
        unset($_SESSION['carrito'][$product_id]);
    }
}

// Manejar la limpieza del carrito
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['carrito']);
}

if (isset($_POST['generate_order'])) {
    // Insertar la orden de venta

    $result = $conn->query("SELECT MAX(id) AS max_id FROM orden_venta");
    $row = $result->fetch_assoc();
    $last_id = $row['max_id'];
    $orden_id = 'OV' . str_pad((int)substr($last_id, 2) + 1, 3, '0', STR_PAD_LEFT);

    $fecha = date('Y-m-d');
    $query = "INSERT INTO orden_venta (id, empleado_id, fecha) VALUES ('$orden_id', '$user_id', '$fecha')";
    if ($conn->query($query)) {
        foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
            $query_producto = "SELECT precio FROM productos WHERE id = '$producto_id'";
            $result_producto = $conn->query($query_producto);
            $precio_unitario = $result_producto->fetch_assoc()['precio'];

            // Insertar en detalle_orden
            $result = $conn->query("SELECT MAX(id) AS max_id FROM detalle_orden");
            $row = $result->fetch_assoc();
            $last_id = $row['max_id'];
            $detalle_id = 'DO' . str_pad((int)substr($last_id, 2) + 1, 3, '0', STR_PAD_LEFT); // Genera un ID de detalle único
            $query_detalle = "INSERT INTO detalle_orden (id, id_orden, id_producto, cantidad, precio_unitario) 
                              VALUES ('$detalle_id', '$orden_id', '$producto_id', '$cantidad', '$precio_unitario')";
            $conn->query($query_detalle);
        }
        // Limpiar carrito
        unset($_SESSION['carrito']);
        // Redirigir a la página de confirmación con el ID de la orden
        header("Location: confirmacion.php?orden_id=$orden_id");
        exit();
    } else {
        die("Error en la inserción de la orden: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl">Carrito</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Productos</a></li>
                <?php if ($tipo_empleado == 'administrador') : ?>
                    <li><a href="agregar_categoria.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Agregar Categoría</a></li>
                    <li><a href="agregar_producto.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Agregar Producto</a></li>
                <?php endif; ?>
            </ul>
        </nav>
</div>
    </header>
    
    <main class="p-4">
        <h2 class="text-xl font-bold mb-4">Carrito de Compras</h2>
        <div class="bg-white shadow-md rounded-lg p-4">
            <form method="POST" action="">
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="border-b p-2">Producto</th>
                            <th class="border-b p-2">Cantidad</th>
                            <th class="border-b p-2">Precio Unitario</th>
                            <th class="border-b p-2">Total</th>
                            <th class="border-b p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['carrito'])) : ?>
                            <?php
                            $total = 0;
                            foreach ($_SESSION['carrito'] as $producto_id => $cantidad) :
                                $query_producto = "SELECT descripcion, precio FROM productos WHERE id = '$producto_id'";
                                $result_producto = $conn->query($query_producto);
                                $producto = $result_producto->fetch_assoc();
                                $total_producto = $producto['precio'] * $cantidad;
                                $total += $total_producto;
                            ?>
                                <tr>
                                    <td class="border-b p-2"><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                                    <td class="border-b p-2">
                                        <input type="number" name="quantity" value="<?php echo htmlspecialchars($cantidad); ?>" min="1" class="w-16 p-2 border rounded">
                                    </td>
                                    <td class="border-b p-2">$<?php echo htmlspecialchars($producto['precio']); ?></td>
                                    <td class="border-b p-2">$<?php echo htmlspecialchars($total_producto); ?></td>
                                    <td class="border-b p-2">
                                        <button type="submit" name="update_quantity" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Actualizar</button>
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($producto_id); ?>">
                                        <button type="submit" name="remove_product" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-rose-700">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="font-bold p-2 text-right">Total</td>
                                <td class="font-bold p-2">$<?php echo htmlspecialchars($total); ?></td>
                                <td></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center p-4">Tu carrito está vacío.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4">
                    <button type="submit" name="clear_cart" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-600 mr-2">Vaciar Carrito</button>
                    <button type="submit" name="generate_order" class="bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Generar Orden</button>
                </div>
            </form>
        </div>
    </main>
    
    <footer class="text-slate-700 p-4 text-center">
        <p> No se añadieron más productos</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
