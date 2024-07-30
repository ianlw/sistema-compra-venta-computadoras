<?php
// login_process.php

session_start();
include '../db.php';

$error_message = "";

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
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Nombre de usuario incorrecto.";
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
    <title>Iniciar Sesión</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Iniciar Sesión</h2>
        <form action="login.php" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <?php if ($error_message == "Nombre de usuario incorrecto.") : ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700">Contraseña:</label>
                <input type="password" id="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <?php if ($error_message == "Contraseña incorrecta.") : ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-900/90 transition">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
