<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

$search_query = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Consultar los productos en función de la búsqueda
$query = "SELECT p.id, p.descripcion, p.foto, p.marca, p.modelo, p.stock_actual, p.precio, c.nombre_categoria, pr.razon_social
          FROM productos p
          INNER JOIN categorias c ON p.categoria_id = c.id
          INNER JOIN proveedores pr ON p.proveedor_id = pr.id
          WHERE p.descripcion LIKE ? OR p.marca LIKE ? OR p.modelo LIKE ? OR c.nombre_categoria LIKE ? OR pr.razon_social LIKE ?";
$stmt = $conn->prepare($query);
$search_param = "%" . $search_query . "%";
$stmt->bind_param('sssss', $search_param, $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Productos - Sistema de Compra y Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4">
        <h1 class="text-2xl">Buscar Productos</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:underline">Inicio</a></li>
                <li><a href="login.html" class="hover:underline">Iniciar Sesión</a></li>
                <li><a href="productos.php" class="hover:underline">Productos</a></li>
                <?php if ($tipo_empleado == 'administrador') : ?>
                    <li><a href="agregar_categoria.php" class="hover:underline">Agregar Categoría</a></li>
                    <li><a href="agregar_producto.php" class="hover:underline">Agregar Producto</a></li>
                <?php endif; ?>
                <li><a href="carrito.php" class="hover:underline">Carrito</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="p-4">
        <h2 class="text-xl font-bold mb-4">Buscar Productos</h2>
        <form method="GET" action="buscar_producto.php" class="mb-4">
            <input type="text" name="search" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($search_query); ?>" class="p-2 border rounded w-full">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2">Buscar</button>
        </form>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <img src="<?php echo htmlspecialchars("./../img/productos/" . $row['foto']); ?>" alt="<?php echo htmlspecialchars($row['descripcion']); ?>" class="w-full h-48 object-cover rounded-t-lg">
                    <div class="p-4">
                        <h3 class="text-lg font-bold"><?php echo htmlspecialchars($row['descripcion']); ?></h3>
                        <p class="text-gray-700">Marca: <?php echo htmlspecialchars($row['marca']); ?></p>
                        <p class="text-gray-700">Modelo: <?php echo htmlspecialchars($row['modelo']); ?></p>
                        <p class="text-gray-700">Stock Actual: <?php echo htmlspecialchars($row['stock_actual']); ?></p>
                        <p class="text-gray-700">Precio: $<?php echo htmlspecialchars($row['precio']); ?></p>
                        <p class="text-gray-700">Categoría: <?php echo htmlspecialchars($row['nombre_categoria']); ?></p>
                        <p class="text-gray-700">Proveedor: <?php echo htmlspecialchars($row['razon_social']); ?></p>
                        <form method="POST" action="productos.php">
                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="number" name="cantidad" value="1" min="1" class="w-full p-2 border rounded mb-2">
                            <button type="submit" name="add_to_cart" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Añadir al carrito</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
    
    <footer class="bg-blue-600 text-white p-4 text-center">
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>

