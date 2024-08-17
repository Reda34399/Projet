<?php
include 'connection.php'; 
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: signin.php'); 
    exit();
}

if (isset($_POST['category_name'])) {
    $categoryName = $_POST['category_name'];

    $deleteProductsQuery = "DELETE FROM Products WHERE category_id = (SELECT category_id FROM Categories WHERE name = ?)";
    $stmt = mysqli_prepare($connection, $deleteProductsQuery);
    mysqli_stmt_bind_param($stmt, "s", $categoryName);
    mysqli_stmt_execute($stmt);

    $query = "DELETE FROM Categories WHERE name = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $categoryName);

    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success'>Catégorie supprimée avec succès.</div>";
        header("Location: admin.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression de la catégorie: " . mysqli_error($connection) . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Nom de catégorie non fourni.</div>";
}
?>
