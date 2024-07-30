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
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray text-gray-900 min-h-screen flex flex-col">
    <header class="sticky top-0 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-3 pb-3 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center text-center place-content-cente">
            <h1 class="text-white font-bold text-center text-2xl ">Bienvenido, <?php echo htmlspecialchars($nombres . ' ' . $apellidos); ?></h1>
    
    <nav class="p-4 text-white">
        <ul class="container mx-auto flex">
            <?php if ($tipo_empleado == 'vendedor') : ?>
                <!-- <li><a href="./ventas/caja.php" class="hover:underline">Gestionar Ordenes de Ventas</a></li> -->
                <li><a href="productos/productos.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Ver Productos</a></li>
            <?php elseif ($tipo_empleado == 'cajero') : ?>
                <li><a href="./ventas/caja.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Caja</a></li>
                <li><a href="./clientes/clientes.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Gestionar Clientes</a></li>
                <li><a href="ventas/ventas.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Gestionar Ventas</a></li>
            <?php elseif ($tipo_empleado == 'administrador') : ?>
                <li><a href="./clientes/clientes.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Gestionar Clientes</a></li>
                <li><a href="empleados/empleados.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Gestionar Empleados</a></li>
                <li><a href="compras/compras.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Gestionar Compras</a></li>
                <li><a href="proveedores/proveedores.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Gestionar Proveedores</a></li>
                <li><a href="productos/productos.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Gestionar Productos</a></li>
                <li><a href="ventas/ventas.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Gestionar Ventas</a></li>
                <li><a href="./reportes/producto_diario.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Ver Reportes</a></li>
            <?php endif; ?>
            <li><a href="./user/form_change_password.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Cambiar Contraseña</a></li>
            <li><a href="./login/logout.php" class="flex rounded-full py-2 px-4 hover:bg-slate-900/50">Cerrar Sesión</a></li>
        </ul>
    </nav>
        </div>
    </header>
    
    <main class="container mx-auto p-4 flex-grow">
        <section class="bg-white p-6 rounded-xl shadow-lg">
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

        <!--
         * <?php if ($tipo_empleado == 'vendedor') : ?>
         *     <section class="bg-white p-6 rounded-lg shadow-md mt-6">
         *         <h2 class="text-xl font-semibold mb-4">Panel de Vendedor</h2>
         *         <p>Contenido exclusivo para vendedores.</p>
         *     </section>
         * <?php elseif ($tipo_empleado == 'cajero') : ?>
         *     <section class="bg-white p-6 rounded-lg shadow-md mt-6">
         *         <h2 class="text-xl font-semibold mb-4">Panel de Cajero</h2>
         *         <p>Contenido exclusivo para cajeros.</p>
         *     </section>
         * <?php elseif ($tipo_empleado == 'administrador') : ?>
         *     <section class="bg-white p-6 rounded-lg shadow-md mt-6">
         *         <h2 class="text-xl font-semibold mb-4">Panel de Administrador</h2>
         *         <p>Contenido exclusivo para administradores.</p>
         *     </section>
         * <?php endif; ?>
        -->
  
    </main>
</body>
</html>
