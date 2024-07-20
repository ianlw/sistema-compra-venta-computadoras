<?php
// login_process.php

session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consultar el usuario en la base de datos
    $stmt = $conn->prepare("
        SELECT u.id, u.empleado_id, u.password, e.tipo 
        FROM usuarios u
        INNER JOIN empleados e ON u.empleado_id = e.id
        WHERE u.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $empleado_id, $hashed_password, $tipo_empleado);
    
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        // Verificar la contraseña
        if (password_verify($password, $hashed_password)) {
            // Contraseña correcta
            $_SESSION['user_id'] = $id;
            $_SESSION['empleado_id'] = $empleado_id;
            $_SESSION['username'] = $username;
            $_SESSION['tipo_empleado'] = $tipo_empleado;

            // Redirigir al dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Nombre de usuario incorrecto.";
    }

    $stmt->close();
    $conn->close();
}
?>
