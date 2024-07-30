<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../db.php';

// Inicializar variables de sesión para errores
$_SESSION['error_current_password'] = '';
$_SESSION['error_new_password'] = '';
$_SESSION['success_message'] = '';
$_SESSION['error_message'] = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // ID del usuario actual
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Verificar que las contraseñas nuevas coinciden
    if ($new_password !== $confirm_new_password) {
        $_SESSION['error_new_password'] = "Las nuevas contraseñas no coinciden.";
        header("Location: form_change_password.php");
        exit();
    }

    // Consultar la contraseña actual del usuario
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    // Si no se encontró el usuario o no tiene contraseña
    if ($stmt->num_rows == 0) {
        $_SESSION['error_message'] = "No se pudo encontrar el usuario.";
        header("Location: form_change_password.php");
        exit();
    }

    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Verificar la contraseña actual
    if (!password_verify($current_password, $hashed_password)) {
        $_SESSION['error_current_password'] = "La contraseña actual es incorrecta.";
        header("Location: form_change_password.php");
        exit();
    }

    // Encriptar la nueva contraseña
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Actualizar la contraseña en la base de datos
    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_hashed_password, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Contraseña cambiada exitosamente.";
    } else {
        $_SESSION['error_message'] = "Error al cambiar la contraseña: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: form_change_password.php");
    exit();
}
?>
