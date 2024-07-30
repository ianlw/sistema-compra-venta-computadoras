<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Incluir el archivo de configuración para obtener BASE_PATH
include __DIR__ . '/config.php';

// Incluir el archivo add_user.php usando una ruta absoluta
include BASE_PATH . '/user/add_user.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Compra y Venta</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="backdrop-blur-sm sticky top-3 left-0 right-0 text-center z-10 bg-slate-900/90 text-white shadow-xl pt-6 pb-6 pr-6 pl-6 mb-3 rounded-xl mt-3 mx-4">
        <h1 class="text-white font-bold text-center text-2xl">Sistema de Compra y Venta de Computadoras</h1>
    </header>
    
    <main class="container mx-auto p-4">
        <section class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Descripción</h2>
            <p class="text-gray-700">Este es un sistema web contruido en base a PHP y MariaDB, además, se usó Tailwindcss para el manejo de el aspecto visual.
            <p class="text-gray-700">En este sistema se emulan los procesos que realiza una empresa con local físico cuyo fin es la venta de productos relacionados a computadoras. </p>
        </section>
        
        <section class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Uso - Inicio de sesión</h2>
            <p class="text-gray-700">Cada empleado que se encuentra en la base de datos está a su vez registrado como usuario. El nombre de usuario de un empleado serán sus nombre y apellidos juntos, sin espacios y en minusculas. La contraseña para todas las cuentas es la misma. Puedes iniciar sesión con estos usuarios: 
<br>
<br>
            <h2 class="text-xl font-semibold mb-4">Vendedor</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li><strong> Nombre se usuario:</strong> juanperez</li>
                <li><strong> Contraseña: </strong>default</li>
            </ul>

<br>
            <h2 class="text-xl font-semibold mb-4">Cajero</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li><strong> Nombre se usuario:</strong> analopez</li>
                <li><strong> Contraseña: </strong>default</li>
            </ul>

<br>
            <h2 class="text-xl font-semibold mb-4">Administrador</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li><strong> Nombre se usuario: </strong>carlosmartinez</li>
                <li><strong> Contraseña: </strong>default</li>
            </ul>
<br>
<p>Puedes iniciar sesión aquí</p>
            <a href="login/login.html" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">Iniciar Sesión</a>
        </section>
    </main>
    

</body>
</html>

<!-- Ian Quispe Ventura -->
