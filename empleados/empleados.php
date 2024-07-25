<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Consultar los empleados
$query = "SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email
          FROM empleados";
$result = $conn->query($query);

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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl">Empleados</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="index.php" class="text-white hover:underline">Inicio</a></li>
                    <li><a href="login.html" class="text-white hover:underline">Iniciar Sesión</a></li>
                    <li><a href="empleados.php" class="text-white hover:underline">Empleados</a></li>
                    <?php if ($tipo_empleado == 'administrador') : ?>
                        <li><a href="registrar_empleado.php" class="text-white hover:underline">Agregar Empleado</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Lista de Empleados</h2>
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
    
    <footer class="bg-blue-600 text-white text-center p-4 mt-6">
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
    
    <?php
    // Cerrar la conexión
    $conn->close();
    ?>
</body>
</html>
