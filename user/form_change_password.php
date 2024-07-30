<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6">Cambiar Contraseña</h2>
        <?php
        session_start();
        if (isset($_SESSION['success_message']) && $_SESSION['success_message']) {
            echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">'.htmlspecialchars($_SESSION['success_message']).'</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
            echo '<div class="bg-red-100 text-red-800 p-4 rounded mb-4">'.htmlspecialchars($_SESSION['error_message']).'</div>';
            unset($_SESSION['error_message']);
        }
        ?>
        <form action="change_password.php" method="post">
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña Actual:</label>
                <input type="password" id="current_password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-xl" required>
                <?php
                if (isset($_SESSION['error_current_password']) && $_SESSION['error_current_password']) {
                    echo '<p class="text-red-600 text-sm mt-2">'.htmlspecialchars($_SESSION['error_current_password']).'</p>';
                    unset($_SESSION['error_current_password']);
                }
                ?>
            </div>
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña:</label>
                <input type="password" id="new_password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-xl" required>
            </div>
            <div class="mb-4">
                <label for="confirm_new_password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" class="w-full px-3 py-2 border border-gray-300 rounded-xl" required>
                <?php
                if (isset($_SESSION['error_new_password']) && $_SESSION['error_new_password']) {
                    echo '<p class="text-red-600 text-sm mt-2">'.htmlspecialchars($_SESSION['error_new_password']).'</p>';
                    unset($_SESSION['error_new_password']);
                }
                ?>
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white p-2 rounded-xl">Cambiar Contraseña</button>
        </form>
        <div class="mt-4 text-center">
            <a href="../dashboard.php" class="text-blue-500 hover:underline">Regresar a Home</a>
        </div>
    </div>
</body>
</html>
