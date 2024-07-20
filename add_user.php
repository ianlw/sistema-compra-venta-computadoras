<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // Incluye el archivo de configuración de la base de datos

// Datos de los usuarios
$usuarios = [
    ['E001', 'demo', 'demo'],
    ['E002', 'ana.lopez', 'default'],
    ['E003', 'carlos.martinez', 'default'],
    ['E004', 'laura.gomez', 'default'],
    ['E005', 'jorge.ramirez', 'default']
];

// Preparar la consulta SQL
$stmt = $conn->prepare("INSERT INTO usuarios (empleado_id, username, password) VALUES (?, ?, ?)");

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Agregar cada usuario
foreach ($usuarios as $usuario) {
    $empleado_id = $usuario[0];
    $username = $usuario[1];
    $password = password_hash($usuario[2], PASSWORD_DEFAULT); // Encriptar la contraseña

    $stmt->bind_param("sss", $empleado_id, $username, $password);

    if (!$stmt->execute()) {
        echo "Error al agregar el usuario: " . $stmt->error . "<br>";
    } else {
    }
}

echo "Usuarios agregados exitosamente.";
$stmt->close();
$conn->close();
?>
