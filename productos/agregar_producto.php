<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['tipo_empleado'] != 'administrador') {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Inicializar variables para los datos de categorías, proveedores y compras
$categorias = [];
$proveedores = [];
$compras = [];

// Obtener categorías, proveedores y compras para las listas desplegables
$categorias_result = $conn->query("SELECT id, nombre_categoria FROM categorias");
$proveedores_result = $conn->query("SELECT id, razon_social FROM proveedores");
$compras_result = $conn->query("SELECT id, tipo_comprobante, nro_comprobante FROM compras");

if ($categorias_result && $proveedores_result && $compras_result) {
    while ($row = $categorias_result->fetch_assoc()) {
        $categorias[] = $row;
    }
    while ($row = $proveedores_result->fetch_assoc()) {
        $proveedores[] = $row;
    }
    while ($row = $compras_result->fetch_assoc()) {
        $compras[] = $row;
    }
}

// Procesar formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $descripcion = $_POST['descripcion'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $stock_inicial = $_POST['stock_inicial'];
    $stock_actual = $_POST['stock_actual'];
    $categoria_id = $_POST['categoria_id'];
    $proveedor_id = $_POST['proveedor_id'];
    $precio = $_POST['precio'];
    $compra_id = $_POST['compra_id'];

    // Procesar la foto
    $foto = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_path = '../img/productos/' . $foto;

    if (move_uploaded_file($foto_tmp, $foto_path)) {
        // Obtener el nuevo ID
        $result = $conn->query("SELECT id FROM productos ORDER BY id DESC LIMIT 1");
        $last_id = $result->fetch_assoc()['id'];

        // Calcular el nuevo ID
        if ($last_id) {
            $last_id_number = (int)substr($last_id, 1); // Eliminar la letra y convertir a número
            $new_id_number = $last_id_number + 1;
        } else {
            $new_id_number = 1; // Si no hay productos, comenzar con 1
        }
        $new_id = 'P' . str_pad($new_id_number, 3, '0', STR_PAD_LEFT);

        // Insertar datos en la base de datos
        $sql = "INSERT INTO productos (id, descripcion, foto, marca, modelo, stock_inicial, stock_actual, categoria_id, proveedor_id, precio, compra_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiissis", $new_id, $descripcion, $foto, $marca, $modelo, $stock_inicial, $stock_actual, $categoria_id, $proveedor_id, $precio, $compra_id);

        if ($stmt->execute()) {
            $mensaje = "Producto agregado exitosamente.";
        } else {
            $mensaje = "Error al agregar el producto: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "Error al subir la foto.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-white text-center text-2xl">Registrar producto</h1>
        <nav class="mt-2">
            <ul class="flex justify-center space-x-4">
                <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                <li><a href="../compras/compras.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar compras</a></li>
                <li><a href="productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Listar Productos</a></li>
            </ul>
        </nav>
            </div>
    </header>


    <main class="container mx-auto p-4">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- <h1 class="text-2xl font-bold mb-4">Agregar Producto</h1> -->
        
        <!-- Mostrar mensaje de éxito o error -->
        <?php if (isset($mensaje)): ?>
            <div class="mb-4 p-4 bg-<?php echo strpos($mensaje, 'Error') === false ? 'green' : 'red'; ?>-500 text-white rounded">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="agregar_producto.php" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 font-semibold mb-2">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>

            <div class="mb-4">
                <label for="foto" class="block text-gray-700 font-semibold mb-2">Foto:</label>
                <input type="file" id="foto" name="foto" class="w-full px-3 py-2 border border-gray-300 rounded" accept="image/*" required>
            </div>

            <div class="mb-4">
                <label for="marca" class="block text-gray-700 font-semibold mb-2">Marca:</label>
                <input type="text" id="marca" name="marca" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>

            <div class="mb-4">
                <label for="modelo" class="block text-gray-700 font-semibold mb-2">Modelo:</label>
                <input type="text" id="modelo" name="modelo" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>

            <div class="mb-4">
                <label for="stock_inicial" class="block text-gray-700 font-semibold mb-2">Stock Inicial:</label>
                <input type="number" id="stock_inicial" name="stock_inicial" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>

            <div class="mb-4">
                <label for="stock_actual" class="block text-gray-700 font-semibold mb-2">Stock Actual:</label>
                <input type="number" id="stock_actual" name="stock_actual" class="w-full px-3 py-2 border border-gray-300 rounded" required>
            </div>

            <div class="mb-4">
                <label for="categoria_id" class="block text-gray-700 font-semibold mb-2">Categoría:</label>
                <select id="categoria_id" name="categoria_id" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['id']); ?>">
                            <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="proveedor_id" class="block text-gray-700 font-semibold mb-2">Proveedor:</label>
                <select id="proveedor_id" name="proveedor_id" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                            <?php echo htmlspecialchars($proveedor['razon_social']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="precio" class="block text-gray-700 font-semibold mb-2">Precio:</label>
                <input type="number" id="precio" name="precio" class="w-full px-3 py-2 border border-gray-300 rounded" step="0.01" required>
            </div>

            <div class="mb-4">
                <label for="compra_id" class="block text-gray-700 font-semibold mb-2">Compra:</label>
                <select id="compra_id" name="compra_id" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <?php foreach ($compras as $compra): ?>
                        <option value="<?php echo htmlspecialchars($compra['id']); ?>">
                            <?php echo htmlspecialchars($compra['tipo_comprobante']) . ' - ' . htmlspecialchars($compra['nro_comprobante']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Agregar Producto</button>
            </div>
        </form>
    </div>
    </main>

</body>
</html>
