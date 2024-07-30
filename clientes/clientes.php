<?php
session_start();
//if (empty($_SESSION['user_id'])) {
 //   header("Location: ../login.html");
   // exit();
//}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

$search_query = "";
$search_results = [];

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $conn->prepare("SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email
                            FROM clientes
                            WHERE id LIKE ? OR nombres LIKE ? OR apellidos LIKE ? OR numero_documento LIKE ?");
    $search_term = "%".$search_query."%";
    $stmt->bind_param("ssss",$search_term, $search_term, $search_term, $search_term);
} else {
    $stmt = $conn->prepare("SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email
                            FROM clientes");
}

$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Error en la consulta: " . $conn->error);
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Clientes - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="sticky top-0 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Gestionar Clientes</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Home</a></li>
                    <li><a href="../ventas/caja.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Caja</a></li>
                    <?php if ($tipo_empleado == 'administrador' || $tipo_empleado == 'cajero') : ?>
                        <li><a href="registrar_cliente.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Agregar Cliente</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto mt-6">
        <h2 class="text-xl font-semibold mb-4">Buscar Clientes</h2>
        <form method="get" action="clientes.php" class="mb-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Buscar por nombre, apellido o documento" class="p-2 border border-gray-300 rounded">
            <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded">Buscar</button>
        </form>
        
        <?php if (!empty($search_results)) : ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-400">
                    <thead class="bg-slate-900/90 text-white">
                        <tr>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Nombres</th>
                            <th class="px-4 py-2 border">Apellidos</th>
                            <th class="px-4 py-2 border">Sexo</th>
                            <th class="px-4 py-2 border">Tipo de Documento</th>
                            <th class="px-4 py-2 border">Número de Documento</th>
                            <th class="px-4 py-2 border">Dirección</th>
                            <th class="px-4 py-2 border">Teléfono</th>
                            <th class="px-4 py-2 border">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $row) : ?>
                            <tr>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['id']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['nombres']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['apellidos']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['sexo']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['tipo_documento']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['numero_documento']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['direccion']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['telefono']); ?></td>
                                <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['email']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p>No se encontraron resultados para "<?php echo htmlspecialchars($search_query); ?>".</p>
        <?php endif; ?>
    </main>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
