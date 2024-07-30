<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Inicializar la variable de búsqueda
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Consultar los empleados con o sin término de búsqueda
$query = "SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email
          FROM empleados
          WHERE CONCAT(nombres, ' ', apellidos) LIKE ? OR
                id LIKE ? OR
                numero_documento LIKE ?
          ORDER BY id";

$stmt = $conn->prepare($query);
$search_term_like = '%' . $search_term . '%';
$stmt->bind_param("sss", $search_term_like, $search_term_like, $search_term_like);
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
    <title>Empleados - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl">Empleados</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900">Home</a></li>
                    <?php if ($tipo_empleado == 'administrador') : ?>
                        <li><a href="registrar_empleado.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900">Agregar Empleado</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Lista de Empleados</h2>

<div class="flex justify-center">
        <form method="GET" action="empleados.php" class="mb-4">
            <div class="flex items-center">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Buscar empleados" class="w-80 px-3 py-2 border border-gray-300 rounded-xl" />
                <button type="submit" class="ml-4 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-900/90">Buscar</button>
            </div>
        </form>
</div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-300">ID</th>
                        <th class="py-2 px-4 border-b border-gray-300">Nombres</th>
                        <th class="py-2 px-4 border-b border-gray-300">Apellidos</th>
                        <th class="py-2 px-4 border-b border-gray-300">Sexo</th>
                        <th class="py-2 px-4 border-b border-gray-300">Tipo de Documento</th>
                        <th class="py-2 px-4 border-b border-gray-300">Número de Documento</th>
                        <th class="py-2 px-4 border-b border-gray-300">Dirección</th>
                        <th class="py-2 px-4 border-b border-gray-300">Teléfono</th>
                        <th class="py-2 px-4 border-b border-gray-300">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr class="bg-white border-b border-gray-200">
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['nombres']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['apellidos']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['sexo']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['tipo_documento']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['numero_documento']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['direccion']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <footer class="text-slate-700 p-4 text-center">
        <p> No existen más empleados</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
