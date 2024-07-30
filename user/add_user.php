<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Incluir el archivo de configuración para obtener BASE_PATH
include __DIR__ . '/../config.php';

// Usar BASE_PATH para incluir db.php
include BASE_PATH . '/db.php';

// Obtener todos los empleados que no tienen un registro en la tabla usuarios
$query = "SELECT id, CONCAT(nombres, apellidos) AS username FROM empleados WHERE id NOT IN (SELECT empleado_id FROM usuarios)";
$result = $conn->query($query);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Preparar la consulta SQL para insertar nuevos usuarios
    $stmt = $conn->prepare("INSERT INTO usuarios (empleado_id, username, password) VALUES (?, ?, ?)");

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $empleado_id = $row['id'];
        $username = strtolower(str_replace(' ', '', $row['username'])); // Eliminar espacios y convertir a minúsculas
        $password = password_hash('default', PASSWORD_DEFAULT); // Encriptar la contraseña

        $stmt->bind_param("sss", $empleado_id, $username, $password);

        if (!$stmt->execute()) {
            //echo "Error al agregar el usuario para el empleado ID $empleado_id: " . $stmt->error . "<br>";
        } else {
            //echo "Usuario agregado exitosamente para el empleado ID $empleado_id.<br>";
        }
    }

    $stmt->close();
} else {
    //echo "Todos los empleados ya tienen un usuario registrado.<br>";
}

$conn->close();
?>
