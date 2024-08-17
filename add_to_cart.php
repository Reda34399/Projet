<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php'); 
    exit();
}

if (isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

   
    $query_check = "SELECT * FROM Cart WHERE user_id = ? AND product_id = ?";
    $stmt_check = mysqli_prepare($connection, $query_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
    
        $query_update = "UPDATE Cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt_update = mysqli_prepare($connection, $query_update);
        mysqli_stmt_bind_param($stmt_update, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt_update);
    } else {
        
        $query_insert = "INSERT INTO Cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt_insert = mysqli_prepare($connection, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt_insert);
    }

    
    mysqli_stmt_close($stmt_check);
    if (isset($stmt_update)) {
        mysqli_stmt_close($stmt_update);
    }
    if (isset($stmt_insert)) {
        mysqli_stmt_close($stmt_insert);
    }

    header('Location: account.php');
    exit();
}
?>
