<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Obtener el término de búsqueda
$search_term = isset($_POST['search']) ? $_POST['search'] : '';

// Consultar los proveedores con el término de búsqueda
$query = "SELECT id, razon_social, ruc, direccion, telefono, email
          FROM proveedores
          WHERE id LIKE ? OR razon_social LIKE ? OR ruc LIKE ? OR direccion LIKE ? OR telefono LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($query);
$search_like = "%$search_term%";
$stmt->bind_param('ssssss', $search_like, $search_like, $search_like, $search_like, $search_like, $search_like);
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
    <title>Proveedores - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Proveedores</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Home</a></li>
                    <?php if ($tipo_empleado == 'administrador') : ?>
                        <li><a href="registrar_proveedor.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/70">Agregar Proveedor</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-6 bg-white rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-bold mb-4">Lista de Proveedores</h2>
        <!-- Formulario de búsqueda -->
<div class="flex justify-center">
        <form method="post" action="" class="mb-6 flex items-center">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Buscar proveedores..." class="w-80 px-3 py-2 border border-gray-300 rounded-xl">
            <button type="submit" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
        </form>
</div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 border-b">ID</th>
                        <th class="px-4 py-2 border-b">Razón Social</th>
                        <th class="px-4 py-2 border-b">RUC</th>
                        <th class="px-4 py-2 border-b">Dirección</th>
                        <th class="px-4 py-2 border-b">Teléfono</th>
                        <th class="px-4 py-2 border-b">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['id']); ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['razon_social']); ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['ruc']); ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['direccion']); ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['telefono']); ?></td>
                                <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($row['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="px-4 py-2 border-b text-center">No se encontraron proveedores.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <footer class="text-slate-700 p-4 text-center">
        <p> No existen más proveedores</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
