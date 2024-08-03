<?php
// Configuración de la base de datos
$host = 'localhost'; 
$db = 'sistema_compra_venta';
$user = 'root'; 
$pass = ''; 

// Crear una conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}else{
}
?>


