<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
include '../db.php'; // Incluye la conexión a la base de datos

$tipo_empleado = $_SESSION['tipo_empleado'];

// Verificar si el usuario es administrador
if ($tipo_empleado !== 'administrador') {
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
        echo "Cliente registrado con éxito. El ID asignado es: " . htmlspecialchars($nuevo_id);
    } else {
        echo "Error al registrar el cliente: " . $stmt->error;
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
</head>
<body>
    <h1>Registrar Nuevo Cliente</h1>
    <form action="registrar_cliente.php" method="post">
        <label>ID (Automático):</label>
        <input type="text" value="<?php echo htmlspecialchars($nuevo_id); ?>" readonly><br>
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" required><br>
        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required><br>
        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
        </select><br>
        <label for="tipo_documento">Tipo de Documento:</label>
        <select id="tipo_documento" name="tipo_documento" required>
            <option value="DNI">DNI</option>
            <option value="CE">CE</option>
            <option value="pasaporte">Pasaporte</option>
        </select><br>
        <label for="numero_documento">Número de Documento:</label>
        <input type="text" id="numero_documento" name="numero_documento" required><br>
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" required><br>
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <input type="submit" value="Registrar Cliente">
    </form>
</body>
</html>
