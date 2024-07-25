<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['tipo_empleado'] != 'administrador') {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

// Consultar categorías y proveedores para los desplegables
$categorias = $conn->query("SELECT id, nombre_categoria FROM categorias");
$proveedores = $conn->query("SELECT id, razon_social FROM proveedores");

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['descripcion'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $stock_inicial = $_POST['stock_inicial'];
    $stock_actual = $_POST['stock_actual'];
    $categoria_id = $_POST['categoria_id'];
    $proveedor_id = $_POST['proveedor_id'];
    $precio = $_POST['precio'];

    // Validar los datos
    if (empty($descripcion) || empty($marca) || empty($modelo) || empty($stock_inicial) || empty($stock_actual) || empty($categoria_id) || empty($proveedor_id) || empty($precio)) {
        echo "<p>Todos los campos son obligatorios.</p>";
    } else {
        // Verificar si las IDs son válidas
        $categoria_check = $conn->prepare("SELECT COUNT(*) FROM categorias WHERE id = ?");
        $categoria_check->bind_param("s", $categoria_id); // Cambiar a "s" para varchar
        $categoria_check->execute();
        $categoria_check->bind_result($categoria_exists);
        $categoria_check->fetch();
        $categoria_check->close();

        $proveedor_check = $conn->prepare("SELECT COUNT(*) FROM proveedores WHERE id = ?");
        $proveedor_check->bind_param("s", $proveedor_id); // Cambiar a "s" para varchar
        $proveedor_check->execute();
        $proveedor_check->bind_result($proveedor_exists);
        $proveedor_check->fetch();
        $proveedor_check->close();

        if ($categoria_exists == 0 || $proveedor_exists == 0) {
            echo "<p>ID de categoría o proveedor no válido.</p>";
        } else {
            // Preparar y ejecutar la consulta de inserción
            $stmt = $conn->prepare("INSERT INTO productos (id, descripcion, marca, modelo, stock_inicial, stock_actual, categoria_id, proveedor_id, precio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $id = 'P' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); // Generar un ID único
            $stmt->bind_param("ssssiissi", $id, $descripcion, $marca, $modelo, $stock_inicial, $stock_actual, $categoria_id, $proveedor_id, $precio);

            if ($stmt->execute()) {
                echo "<p>Producto agregado exitosamente.</p>";
            } else {
                echo "<p>Error al agregar producto: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Sistema de Compra y Venta</title>
</head>
<body>
    <header>
        <h1>Agregar Producto</h1>
        <nav>
            <ul>
                <li><a href="productos.php">Volver a Productos</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Formulario para Agregar Producto</h2>
        <form method="post" action="">
            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" required>
            <br>
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" required>
            <br>
            <label for="modelo">Modelo:</label>
            <input type="text" id="modelo" name="modelo" required>
            <br>
            <label for="stock_inicial">Stock Inicial:</label>
            <input type="number" id="stock_inicial" name="stock_inicial" required>
            <br>
            <label for="stock_actual">Stock Actual:</label>
            <input type="number" id="stock_actual" name="stock_actual" required>
            <br>
            <label for="categoria_id">Categoría:</label>
            <select id="categoria_id" name="categoria_id" required>
                <?php while ($row = $categorias->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['nombre_categoria']); ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="proveedor_id">Proveedor:</label>
            <select id="proveedor_id" name="proveedor_id" required>
                <?php while ($row = $proveedores->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['razon_social']); ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" required>
            <br>
            <input type="submit" value="Agregar Producto">
        </form>
    </main>
</body>
</html>
