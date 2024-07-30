<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$tipo_empleado = $_SESSION['tipo_empleado'];

// Verificar si el usuario es administrador
if ($tipo_empleado == 'vendedor') {
    header("Location: acceso_denegado.php"); // Redirigir a una página de acceso denegado
    exit();
}

// Función para generar el siguiente ID
function generarNuevoId($conn) {
    // Consultar el último ID
    $result = $conn->query("SELECT id FROM clientes ORDER BY id DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ultimo_id = $row['id'];
        $numero = (int)substr($ultimo_id, 1);
        $nuevo_numero = str_pad($numero + 1, 3, '0', STR_PAD_LEFT);
        return 'C' . $nuevo_numero;
    } else {
        return 'C001';
    }
}

// Obtener el siguiente ID disponible
$nuevo_id = generarNuevoId($conn);

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $sexo = $_POST['sexo'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    // Validar el número de teléfono (debe tener exactamente 9 dígitos)
    if (!preg_match('/^\d{9}$/', $telefono)) {
        die("Error: El número de teléfono debe tener exactamente 9 dígitos.");
    }

    // Preparar y ejecutar la consulta para insertar el nuevo cliente
    $stmt = $conn->prepare("
        INSERT INTO clientes (id, nombres, apellidos, sexo, tipo_documento, numero_documento, direccion, telefono, email)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssssssss", $nuevo_id, $nombres, $apellidos, $sexo, $tipo_documento, $numero_documento, $direccion, $telefono, $email);

    if ($stmt->execute()) {
//        echo "Cliente registrado con éxito. El ID asignado es: " . htmlspecialchars($nuevo_id);
    } else {
//        echo "Error al registrar el cliente: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl">Registrar Cliente</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="../dashboard.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Home</a></li>
                    <li><a href="../ventas/caja.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Caja</a></li>
                    <li><a href="./clientes.php" class="flex rounded-full py-2 px-5 hover:bg-slate-900/50">Listar clientes</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container mx-auto p-4">
        <form action="registrar_cliente.php" method="post" class="bg-white p-6 rounded shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">ID (Automático):</label>
                <input type="text" value="<?php echo htmlspecialchars($nuevo_id); ?>" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="nombres" class="block text-gray-700 font-bold mb-2">Nombres:</label>
                <input type="text" id="nombres" name="nombres" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="apellidos" class="block text-gray-700 font-bold mb-2">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="sexo" class="block text-gray-700 font-bold mb-2">Sexo:</label>
                <select id="sexo" name="sexo" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="tipo_documento" class="block text-gray-700 font-bold mb-2">Tipo de Documento:</label>
                <select id="tipo_documento" name="tipo_documento" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="DNI">DNI</option>
                    <option value="CE">CE</option>
                    <option value="pasaporte">Pasaporte</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="numero_documento" class="block text-gray-700 font-bold mb-2">Número de Documento:</label>
                <input type="text" id="numero_documento" name="numero_documento" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="direccion" class="block text-gray-700 font-bold mb-2">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="telefono" class="block text-gray-700 font-bold mb-2">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between">
                <input type="submit" value="Registrar Cliente" class="bg-slate-900 hover:bg-slate-900/90 text-white font-bold py-2 px-4 rounded-xl focus:outline-none focus:shadow-outline">
            </div>
        </form>
    </div>
</body>
</html>
