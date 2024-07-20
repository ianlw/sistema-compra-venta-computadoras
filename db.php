<?php
// Configuración de la base de datos
$host = '127.0.0.1'; // Cambia esto si tu base de datos está en otro host
$db = 'sistema_compra_venta';
$user = 'root'; // Cambia esto si tu usuario de la base de datos es diferente
$pass = 'wilf18dora'; // Cambia esto si tu contraseña de la base de datos es diferente

// Crear una conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}else{
}
?>


