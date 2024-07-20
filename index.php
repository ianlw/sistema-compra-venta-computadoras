<?php
include "add_users.php";
//include "login.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Compra y Venta</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu archivo CSS si tienes uno -->
</head>
<body>
    <header>
        <h1>Sistema de Compra y Venta de Computadoras</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="login.html">Iniciar Sesión</a></li>
                <li><a href="contacto.html">Contacto</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="welcome">
            <h2>Bienvenido a Nuestro Sistema</h2>
            <p>Este es el sistema de gestión de compra y venta de computadoras. Aquí podrás gestionar empleados, clientes, proveedores, productos, y realizar ventas de manera eficiente.</p>
            <a href="login.html" class="btn">Iniciar Sesión</a>
        </section>
        
        <section class="features">
            <h2>Características del Sistema</h2>
            <ul>
                <li>Gestión de empleados</li>
                <li>Gestión de clientes</li>
                <li>Gestión de proveedores</li>
                <li>Gestión de productos</li>
                <li>Registro y seguimiento de ventas</li>
                <li>Generación de reportes</li>
            </ul>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2024 Sistema de Compra y Venta. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
