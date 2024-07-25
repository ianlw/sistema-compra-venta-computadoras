<?php
include "./../user/add_user.php";
//include "login.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Compra y Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-blue-600 p-4">
        <h1 class="text-white text-center text-2xl">Sistema de Compra y Venta de Computadoras</h1>
        <nav class="mt-2">
            <ul class="flex justify-center space-x-4">
                <li><a href="index.php" class="text-white hover:underline">Inicio</a></li>
                <li><a href="./login/login.html" class="text-white hover:underline">Iniciar Sesión</a></li>
                <li><a href="contacto.html" class="text-white hover:underline">Contacto</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="container mx-auto p-4">
        <section class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Bienvenido a Nuestro Sistema</h2>
            <p class="text-gray-700">Este es el sistema de gestión de compra y venta de computadoras. Aquí podrás gestionar empleados, clientes, proveedores, productos, y realizar ventas de manera eficiente.</p>
            <a href="login/login.html" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">Iniciar Sesión</a>
        </section>
        
        <section class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Características del Sistema</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li>Gestión de empleados</li>
                <li>Gestión de clientes</li>
                <li>Gestión de proveedores</li>
                <li>Gestión de productos</li>
                <li>Registro y seguimiento de ventas</li>
                <li>Generación de reportes</li>
            </ul>
        </section>
    </main>
    
    <footer class="bg-blue-600 text-white text-center p-4 mt-6">
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
