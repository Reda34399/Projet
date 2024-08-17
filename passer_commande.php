<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT product_id, quantity, (SELECT price FROM products WHERE products.product_id = cart.product_id) AS price FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total_amount = 0;
$order_items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $total_amount += $row['price'] * $row['quantity'];
    $order_items[] = $row;
}

$orderQuery = "INSERT INTO orders (user_id, order_date, total_amount) VALUES (?, NOW(), ?)";
$stmt = mysqli_prepare($connection, $orderQuery);
mysqli_stmt_bind_param($stmt, "id", $user_id, $total_amount);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($connection);

foreach ($order_items as $item) {
    $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    mysqli_stmt_execute($stmt);
}

$query = "DELETE FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

header('Location: account.php');
exit();
?>
