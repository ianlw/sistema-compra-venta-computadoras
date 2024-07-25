<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include 'db.php'; // Incluye la conexión a la base de datos

$user_id = $_SESSION['empleado_id'];
$tipo_empleado = $_SESSION['tipo_empleado'];

// Consultar la información del empleado
$stmt = $conn->prepare("
    SELECT id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email, foto
    FROM empleados 
    WHERE id = ?
");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $nombres, $apellidos, $sexo, $tipo_documento, $numero_documento, $direccion, $telefono, $email, $foto);
$stmt->fetch();
$stmt->close();
$conn->close();
$picture = "./img/empleados/" . $foto;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Compra y Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">
    <header class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl">Bienvenido, <?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?></h1>
        </div>
    </header>
    
    <nav class="bg-blue-500 p-4 text-white">
        <ul class="container mx-auto flex space-x-4">
            <?php if ($tipo_empleado == 'vendedor') : ?>
                <li><a href="ventas.php" class="hover:underline">Gestionar Ventas</a></li>
                <li><a href="productos/productos.php" class="hover:underline">Ver Productos</a></li>
            <?php elseif ($tipo_empleado == 'cajero') : ?>
                <li><a href="caja.php" class="hover:underline">Caja</a></li>
                <li><a href="./clientes/clientes.php" class="hover:underline">Gestionar Clientes</a></li>
                <li><a href="reportes.php" class="hover:underline">Ver Reportes</a></li>
            <?php elseif ($tipo_empleado == 'administrador') : ?>
                <li><a href="./clientes/clientes.php" class="hover:underline">Gestionar Clientes</a></li>
                <li><a href="empleados/empleados.php" class="hover:underline">Gestionar Empleados</a></li>
                <li><a href="compras/compras.php" class="hover:underline">Gestionar Compras</a></li>
                <li><a href="proveedores/proveedores.php" class="hover:underline">Gestionar Proveedores</a></li>
                <li><a href="productos/productos.php" class="hover:underline">Gestionar Productos</a></li>
                <li><a href="reportes.php" class="hover:underline">Ver Reportes</a></li>
            <?php endif; ?>
            <li><a href="./user/change_password.html" class="hover:underline">Cambiar Contraseña</a></li>
            <li><a href="logout.php" class="hover:underline">Cerrar Sesión</a></li>
        </ul>
    </nav>
    
    <main class="container mx-auto p-4 flex-grow">
        <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Información del Empleado</h2>
            <div class="grid grid-cols-2 gap-4">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($id); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombres); ?></p>
                <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($apellidos); ?></p>
                <p><strong>Sexo:</strong> <?php echo htmlspecialchars($sexo); ?></p>
                <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($tipo_documento); ?></p>
                <p><strong>Número de Documento:</strong> <?php echo htmlspecialchars($numero_documento); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            </div>
            
            <?php if (!empty($foto)) : ?>
                <div class="mt-4">
                    <p><strong>Foto:</strong></p>
                    <img src="<?php echo $picture; ?>" alt="Foto del empleado" class="w-32 h-auto rounded-full">
                </div>
            <?php endif; ?>
        </section>

        <?php if ($tipo_empleado == 'vendedor') : ?>
            <section class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-xl font-semibold mb-4">Panel de Vendedor</h2>
                <p>Contenido exclusivo para vendedores.</p>
            </section>
        <?php elseif ($tipo_empleado == 'cajero') : ?>
            <section class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-xl font-semibold mb-4">Panel de Cajero</h2>
                <p>Contenido exclusivo para cajeros.</p>
            </section>
        <?php elseif ($tipo_empleado == 'administrador') : ?>
            <section class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-xl font-semibold mb-4">Panel de Administrador</h2>
                <p>Contenido exclusivo para administradores.</p>
            </section>
        <?php endif; ?>
    </main>
    
    <footer class="bg-blue-600 text-white text-center p-4">
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
