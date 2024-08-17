<?php
include 'connection.php'; 
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: signin.php'); 
    exit();
}

if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']); 

    $deleteCartQuery = "DELETE FROM Cart WHERE product_id = ?";
    $stmt = mysqli_prepare($connection, $deleteCartQuery);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);

    $query = "DELETE FROM Products WHERE product_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);

    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success'>Produit supprimé avec succès.</div>";
        header("Refresh:1; url=admin.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression du produit: " . mysqli_error($connection) . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID du produit non fourni.</div>";
}
?>
