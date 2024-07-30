<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Inicializar variables para la búsqueda
$search_query = '';
$filter_query = '';

// Inicializar mensajes
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = ['type' => '', 'text' => ''];
}

// Manejo del formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $filter_query = " WHERE p.descripcion LIKE '%" . $conn->real_escape_string($search_query) . "%'";
}

// Consultar los productos con el filtro de búsqueda si es necesario
$query = "SELECT p.id, p.descripcion, p.foto, p.marca, p.modelo, p.stock_actual, p.precio, c.nombre_categoria, pr.razon_social
          FROM productos p
          INNER JOIN categorias c ON p.categoria_id = c.id
          INNER JOIN proveedores pr ON p.proveedor_id = pr.id" . $filter_query;

$result = $conn->query($query);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    // Si el producto ya está en el carrito, actualiza la cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id] += $cantidad;
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto actualizado en el carrito.'];
    } else {
        $_SESSION['carrito'][$producto_id] = $cantidad;
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto añadido al carrito.'];
    }
    
    // Redirigir para evitar el reenvío del formulario en caso de refrescar la página
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold ">Productos</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <?php if ($tipo_empleado == 'administrador') : ?>
                    <li><a href="agregar_categoria.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Agregar Categoría</a></li>
                    <li><a href="agregar_producto.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Agregar Producto</a></li>
                <?php endif; ?>
                <li><a href="carrito.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Carrito</a></li>
            </ul>
        </nav>
        </div>
    </header>
    
    <main class="p-4">
        <h2 class="text-xl font-bold mb-4">Lista de Productos</h2>

        <!-- Mensaje de notificación -->
        <?php if ($_SESSION['message']['text']): ?>
            <div class="mb-4 p-4 rounded-lg text-white 
                <?= $_SESSION['message']['type'] == 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php $_SESSION['message'] = ['type' => '', 'text' => '']; // Limpiar mensaje ?>
        <?php endif; ?>

        <!-- Formulario de búsqueda -->
<div class="flex justify-center">
        <form method="POST" action="" class="mb-6 flex space-x-4">
            <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Buscar productos" class="w-80 px-3 py-2 border border-gray-300 rounded-xl">
            <button type="submit" name="search" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
        </form>
</div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <img src="<?php echo htmlspecialchars("./../img/productos/" . $row['foto']); ?>" alt="<?php echo htmlspecialchars($row['descripcion']); ?>" class="w-full h-48 object-cover rounded-xl">
                    <div class="p-4">
                        <h3 class="text-lg font-bold"><?php echo htmlspecialchars($row['descripcion']); ?></h3>
                        <p class="text-gray-700">Marca: <?php echo htmlspecialchars($row['marca']); ?></p>
                        <p class="text-gray-700">Modelo: <?php echo htmlspecialchars($row['modelo']); ?></p>
                        <p class="text-gray-700">Stock Actual: <?php echo htmlspecialchars($row['stock_actual']); ?></p>
                        <p class="text-gray-700">Precio: $<?php echo htmlspecialchars($row['precio']); ?></p>
                        <p class="text-gray-700">Categoría: <?php echo htmlspecialchars($row['nombre_categoria']); ?></p>
                        <p class="text-gray-700">Proveedor: <?php echo htmlspecialchars($row['razon_social']); ?></p>
                        <form method="POST" action="">
                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="number" name="cantidad" value="1" min="1" class="w-full p-2 border rounded mb-2">
                            <button type="submit" name="add_to_cart" class="bg-slate-900 text-white px-4 py-2 rounded-xl mt-3 hover:bg-slate-900/90">Añadir al carrito</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
    
    <footer class=" text-slate-700 p-4 text-center">
        <p> No existen más productos </p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
