<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $payment = mysqli_real_escape_string($connection, $_POST['payment']);

$query = "SELECT price FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$totalAmount = $row['price'] * $quantity;


    $orderQuery = "INSERT INTO orders (user_id, order_date, total_amount) VALUES (?, NOW(), ?)";
    $stmt = mysqli_prepare($connection, $orderQuery);
    mysqli_stmt_bind_param($stmt, "id", $userId, $totalAmount);
    mysqli_stmt_execute($stmt);
    $orderId = mysqli_insert_id($connection);

    $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $itemQuery);
    mysqli_stmt_bind_param($stmt, "iiid", $orderId, $productId, $quantity, $row['price']);
    mysqli_stmt_execute($stmt);

    mysqli_close($connection);
    
    header("Location: order_confirmation.php?order_id=" . $orderId);
    exit();
}
?>
