<?php
include 'connection.php';

if (isset($_POST['edit_order'])) {
    $order_id = $_POST['order_id'];
    $customer_name = $_POST['customer_name'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];

    $updateOrderQuery = "
    UPDATE orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    JOIN users u ON o.user_id = u.user_id
    SET u.username = ?, p.name = ?, oi.quantity = ?, o.order_date = ?
    WHERE o.order_id = ?";

    $stmt = mysqli_prepare($connection, $updateOrderQuery);
    mysqli_stmt_bind_param($stmt, "ssisi", $customer_name, $product_name, $quantity, $order_date, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: admin.php?success=Commande mise à jour');
    } else {
        header('Location: admin.php?error=Erreur lors de la mise à jour de la commande');
    }
}
?>
