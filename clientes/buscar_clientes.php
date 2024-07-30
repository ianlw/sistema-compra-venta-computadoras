<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['tipo_empleado'] != 'cajero') {
    header("Location: login.html");
    exit();
}

include '../db.php'; // Incluye la conexión a la base de datos

$clientes = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar'])) {
    $busqueda = '%' . $_POST['buscar'] . '%';
    $stmt = $conn->prepare("
        SELECT id, nombres, apellidos, numero_documento, email 
        FROM clientes
        WHERE id LIKE ? OR nombres LIKE ? OR apellidos LIKE ? OR email LIKE ? OR numero_documento LIKE ?
    ");
    $stmt->bind_param("sssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    $clientes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
    <header class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl">Buscar Cliente</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="hover:underline">Home</a></li>
                    <li><a href="../ventas/caja.php" class="hover:underline">Caja</a></li>
                    <li><a href="./clientes.php" class="hover:underline">Listar clientes</a></li>
                        <li><a href="registrar_cliente.php" class="hover:underline">Agregar Cliente</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4 flex-grow">
        <section class="bg-white p-6 rounded-lg shadow-md">
            <form action="buscar_clientes.php" method="POST" class="mb-4">
                <div class="flex items-center">
                    <input type="text" name="buscar" placeholder="Buscar por nombre, apellido, email o teléfono" class="flex-grow p-2 border border-gray-300 rounded-md">
                    <button type="submit" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded">Buscar</button>
                </div>
            </form>

            <?php if (!empty($clientes)): ?>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">ID</th>
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Apellido</th>
                            <th class="py-2">Email</th>
                            <th class="py-2">Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['id']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['nombres']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['apellidos']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['numero_documento']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <p>No se encontraron resultados para la búsqueda.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="bg-blue-600 text-white text-center p-4">
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

