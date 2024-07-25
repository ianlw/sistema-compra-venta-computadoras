<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // ID del usuario actual
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Verificar que las contraseñas nuevas coinciden
    if ($new_password !== $confirm_new_password) {
        die("Las nuevas contraseñas no coinciden.");
    }

    // Consultar la contraseña actual del usuario
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Verificar la contraseña actual
    if (!password_verify($current_password, $hashed_password)) {
        die("La contraseña actual es incorrecta.");
    }

    // Encriptar la nueva contraseña
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Actualizar la contraseña en la base de datos
    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "Contraseña cambiada exitosamente.";
    } else {
        echo "Error al cambiar la contraseña: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

