<?php
include 'connection.php';

if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $deleteItemsQuery);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);

    $deleteOrderQuery = "DELETE FROM orders WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $deleteOrderQuery);
    mysqli_stmt_bind_param($stmt, "i", $order_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: admin.php?success=Commande supprimée');
    } else {
        header('Location: admin.php?error=Erreur lors de la suppression de la commande');
    }
}
?>
