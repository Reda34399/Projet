<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'] ?? '';
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['product_price'] ?? '';
    $description = $_POST['product_description'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $image = $_FILES['product_image']['name'] ?? '';

    if (!$product_id) {
        die("ID du produit non spécifié.");
    }

    if ($image) {
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_folder = 'uploads/' . $image;
        move_uploaded_file($image_tmp, $image_folder);
    } else {
        $query = "SELECT image FROM Products WHERE product_id=?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $image_folder = $row['image'] ?? ''; 
        $stmt->close();
    }

    $query = "UPDATE Products SET name=?, price=?, description=?, image=?, category_id=? WHERE product_id=?";
    $stmt = $connection->prepare($query);
    
    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $connection->error);
    }

    $stmt->bind_param("ssdssi", $name, $price, $description, $image_folder, $category_id, $product_id);

    if ($stmt->execute()) {
        echo "Produit mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du produit : " . $stmt->error;
    }

    $stmt->close();
}

$connection->close();
?>
