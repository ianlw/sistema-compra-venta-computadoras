<?php
// show_users.php

include 'db.php'; // Incluye el archivo de configuración de la base de datos

// Consultar todos los usuarios
$query = "SELECT empleado_id, username FROM usuarios";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Mostrar los usuarios en una tabla HTML
    echo "<table border='1'>
            <tr>
                <th>ID Empleado</th>
                <th>Nombre de Usuario</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['empleado_id']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No hay usuarios en la base de datos.";
}

$result->free();
$conn->close();
?>

