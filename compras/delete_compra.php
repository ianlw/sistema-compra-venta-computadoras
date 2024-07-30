<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

include '../db.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Imprimir los datos enviados en la solicitud POST
    var_dump($_POST);

    if (isset($_POST['compra_id'])) {
        $compra_id = $_POST['compra_id'];

        // Preparar la consulta para verificar registros en detalle_orden
        $checkDetalleOrden = $conn->prepare("SELECT COUNT(*) FROM detalle_orden WHERE id_producto IN (SELECT id FROM productos WHERE compra_id = ?)");
        $checkDetalleOrden->bind_param("s", $compra_id);
        $checkDetalleOrden->execute();
        $checkDetalleOrden->bind_result($countOrden);
        $checkDetalleOrden->fetch();
        $checkDetalleOrden->close();

        // Preparar la consulta para verificar registros en detalle_venta
        $checkDetalleVenta = $conn->prepare("SELECT COUNT(*) FROM detalle_venta WHERE producto_id IN (SELECT id FROM productos WHERE compra_id = ?)");
        $checkDetalleVenta->bind_param("s", $compra_id);
        $checkDetalleVenta->execute();
        $checkDetalleVenta->bind_result($countVenta);
        $checkDetalleVenta->fetch();
        $checkDetalleVenta->close();

        // Verificar si hay registros relacionados
        if ($countOrden > 0 || $countVenta > 0) {
            $_SESSION['error'] = "No se puede eliminar la compra porque tiene detalles relacionados en órdenes o ventas.";
        } else {
            // Proceder con la eliminación
            $conn->begin_transaction();

            try {
                // Primero eliminamos los productos relacionados con la compra
                $deleteProductos = $conn->prepare("DELETE FROM productos WHERE compra_id = ?");
                $deleteProductos->bind_param("s", $compra_id);
                $deleteProductos->execute();

                // Luego eliminamos la compra
                $deleteCompra = $conn->prepare("DELETE FROM compras WHERE id = ?");
                $deleteCompra->bind_param("s", $compra_id);
                $deleteCompra->execute();

                // Confirmar la transacción
                $conn->commit();
                
                $_SESSION['message'] = "Compra eliminada exitosamente.";
            } catch (Exception $e) {
                // Deshacer la transacción en caso de error
                $conn->rollback();
                $_SESSION['error'] = "Error al eliminar la compra: " . $e->getMessage();
            }
        }

        // Redirigir al listado de compras
        header("Location: compras.php");
        exit();
    }
}
?>
