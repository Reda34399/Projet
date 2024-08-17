<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_id'])) {
        $category_id = $_POST['category_id'];
        $name = $_POST['category_name'] ?? '';
        $description = $_POST['category_description'] ?? '';
        $image = $_FILES['category_image']['name'] ?? '';

        if ($image) {
            $image_tmp = $_FILES['category_image']['tmp_name'];
            $image_folder = 'uploads/' . $image;
            move_uploaded_file($image_tmp, $image_folder);
        } else {
            $image_folder = ''; 
        }

        $query = "UPDATE Categories SET name=?, description=?, image=? WHERE category_id=?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sssi", $name, $description, $image_folder, $category_id);

        if ($stmt->execute()) {
            echo "Catégorie mise à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour de la catégorie : " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "ID de la catégorie non spécifié.";
    }
}

$connection->close();
?>
