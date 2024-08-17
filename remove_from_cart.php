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

    $query = "DELETE FROM Cart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success'>Produit supprimé du panier avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression du produit: " . mysqli_error($connection) . "</div>";
    }
}

header('Location: account.php');
exit();
