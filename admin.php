<?php
include 'connection.php'; 
session_start(); 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: signin.php'); 
    exit();
}

if (isset($_SESSION['form_submitted'])) {
    unset($_SESSION['form_submitted']);
} elseif (isset($_POST['add_category'])) {
    $_SESSION['form_submitted'] = true;

    $categoryName = $_POST['category_name'];
    $categoryDescription = $_POST['category_description'];

    $targetDirectory = "uploads/"; 
    $uploadOk = 1;

    if (!is_dir($targetDirectory)) {
        if (!mkdir($targetDirectory, 0755, true)) { 
            die("<div class='alert alert-danger'>Error: Could not create upload directory.</div>");
        }
    } elseif (!is_writable($targetDirectory)) {
        die("<div class='alert alert-danger'>Error: Upload directory is not writable.</div>");
    }

    $targetFile = $targetDirectory . basename($_FILES["category_image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["category_image"]["tmp_name"]);

    if ($check === false) {
        echo "<div class='alert alert-danger'>The file is not an image.</div>";
        $uploadOk = 0;
    }

    if ($_FILES["category_image"]["size"] > 500000) {
        echo "<div class='alert alert-danger'>File is too large. It should be less than 500KB.</div>";
        $uploadOk = 0;
    }

    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "<div class='alert alert-danger'>Only JPG, JPEG, PNG & GIF files are allowed.</div>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $targetFile)) {
            $query = "INSERT INTO Categories (name, description, image) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sss", $categoryName, $categoryDescription, $targetFile);

            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Category added successfully.</div>";
                header("Refresh:1; url=admin.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error adding category: " . mysqli_error($connection) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading file.</div>";
        }
    }
}



if (isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $categoryId = $_POST['category_id']; 
    $productPrice = $_POST['product_price']; 

    $targetDirectory = "uploads/"; 
    $uploadOk = 1;

    if (!is_dir($targetDirectory)) {
        if (!mkdir($targetDirectory, 0755, true)) { 
            die("<div class='alert alert-danger'>Error: Could not create upload directory.</div>");
        }
    } elseif (!is_writable($targetDirectory)) {
        die("<div class='alert alert-danger'>Error: Upload directory is not writable.</div>");
    }

    $targetFile = $targetDirectory . basename($_FILES["product_image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);

    if ($check === false) {
        echo "<div class='alert alert-danger'>The file is not an image.</div>";
        $uploadOk = 0;
    }

    if ($_FILES["product_image"]["size"] > 500000) {
        echo "<div class='alert alert-danger'>File is too large. It should be less than 500KB.</div>";
        $uploadOk = 0;
    }

    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "<div class='alert alert-danger'>Only JPG, JPEG, PNG & GIF files are allowed.</div>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
            $query = "INSERT INTO Products (name, description, image, price, category_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sssdi", $productName, $productDescription, $targetFile, $productPrice, $categoryId);

            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Product added successfully.</div>";
                header("Refresh:1; url=admin.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error adding product: " . mysqli_error($connection) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading file.</div>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-header {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
        }
        .admin-header h2 {
            margin: 0;
            text-align: center;
        }
        .container {
            margin-top: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-header">
        <h2>Bienvenue, Administrateur!</h2>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Gestion des catégories
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#addCategoryModal">Ajouter une catégorie</button>
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#deleteCategoryModal">Supprimer une catégorie</button>
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#editCategoryModal">Modifier une catégorie</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Gestion des produits
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#addProductModal">Ajouter un produit</button>
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#deleteProductModal">Supprimer un produit</button>
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#editProductModal">Modifier un produit</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Gestion des commandes
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#viewOrdersModal">Voir les commandes</button>
                        <button type="button" class="btn btn-danger mb-2" data-toggle="modal" data-target="#deleteOrdersModal">Supprimer une commande</button>
                        <button type="button" class="btn btn-custom mb-2" data-toggle="modal" data-target="#editOrdersModal">Modifier une commande</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une nouvelle catégorie</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="category_name">Nom de la catégorie:</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>
                            <div class="form-group">
                                <label for="category_description">Description:</label>
                                <textarea class="form-control" id="category_description" name="category_description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category_image">Image de la catégorie:</label>
                                <input type="file" class="form-control-file" id="category_image" name="category_image">
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_category">Ajouter la catégorie</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCategoryModalLabel">Supprimer une catégorie</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="delete_category.php">
                            <div class="form-group">
                                <label for="category_name">Nom de la catégorie à supprimer:</label>
                                <select class="form-control" id="category_name" name="category_name" required>
                                    <?php
                                    $query = "SELECT name FROM Categories";
                                    $result = mysqli_query($connection, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value=\"{$row['name']}\">{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger">Supprimer la catégorie</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Modifier une catégorie</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="update_category.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="category_id">Sélectionnez une catégorie à modifier:</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <?php
                                    $query = "SELECT category_id, name FROM Categories";
                                    $result = mysqli_query($connection, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value=\"{$row['category_id']}\">{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_name">Nouveau nom de la catégorie:</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>
                            <div class="form-group">
                                <label for="category_description">Nouvelle description:</label>
                                <textarea class="form-control" id="category_description" name="category_description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category_image">Nouvelle image de la catégorie (optionnelle):</label>
                                <input type="file" class="form-control-file" id="category_image" name="category_image">
                            </div>
                            <button type="submit" class="btn btn-primary" name="edit_category">Modifier la catégorie</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Ajouter un nouveau produit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="product_name">Nom du produit:</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="form-group">
                                <label for="product_description">Description:</label>
                                <textarea class="form-control" id="product_description" name="product_description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="product_image">Image du produit:</label>
                                <input type="file" class="form-control-file" id="product_image" name="product_image">
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_product">Ajouter le produit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProductModalLabel">Supprimer un produit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="delete_product.php">
                            <div class="form-group">
                                <label for="product_name">Nom du produit à supprimer:</label>
                                <select class="form-control" id="product_name" name="product_name" required>
                                    <?php
                                    $query = "SELECT name FROM Products";
                                    $result = mysqli_query($connection, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value=\"{$row['name']}\">{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger">Supprimer le produit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Modifier un produit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="update_product.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="product_id">Sélectionnez un produit à modifier:</label>
                                <select class="form-control" id="product_id" name="product_id" required>
                                    <?php
                                    $query = "SELECT product_id, name FROM Products";
                                    $result = mysqli_query($connection, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value=\"{$row['product_id']}\">{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product_name">Nouveau nom du produit:</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="form-group">
                                <label for="product_description">Nouvelle description:</label>
                                <textarea class="form-control" id="product_description" name="product_description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="product_image">Nouvelle image du produit (optionnelle):</label>
                                <input type="file" class="form-control-file" id="product_image" name="product_image">
                            </div>
                            <button type="submit" class="btn btn-primary" name="edit_product">Modifier le produit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade" id="viewOrdersModal" tabindex="-1" aria-labelledby="viewOrdersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrdersModalLabel">Liste des commandes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $query = "
                SELECT o.order_id, u.username AS customer_name, p.name AS product_name, oi.quantity, o.order_date 
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN products p ON oi.product_id = p.product_id
                ";
                $result = mysqli_query($connection, $query);
                
                if (mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>ID Commande</th>';
                    echo '<th>Nom du client</th>';
                    echo '<th>Produit</th>';
                    echo '<th>Quantité</th>';
                    echo '<th>Date</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo "<td>{$row['order_id']}</td>";
                        echo "<td>{$row['customer_name']}</td>";
                        echo "<td>{$row['product_name']}</td>";
                        echo "<td>{$row['quantity']}</td>";
                        echo "<td>{$row['order_date']}</td>";
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>Aucune commande trouvée.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>


        <div class="modal fade" id="editOrdersModal" tabindex="-1" aria-labelledby="editOrdersModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrdersModalLabel">Modifier une commande</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="update_order.php">
                            <div class="form-group">
                                <label for="order_id">Sélectionnez une commande à modifier:</label>
                                <select class="form-control" id="order_id" name="order_id" required>
                                    <?php
                                    $query = "SELECT order_id FROM Orders";
                                    $result = mysqli_query($connection, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value=\"{$row['order_id']}\">{$row['order_id']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="customer_name">Nouveau nom du client:</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="form-group">
                                <label for="product_name">Nouveau produit:</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity">Nouvelle quantité:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>
                            <div class="form-group">
                                <label for="order_date">Nouvelle date:</label>
                                <input type="date" class="form-control" id="order_date" name="order_date" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="edit_order">Modifier la commande</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
<div class="modal fade" id="deleteOrdersModal" tabindex="-1" aria-labelledby="deleteOrdersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOrdersModalLabel">Supprimer une commande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="delete_order.php">
                    <div class="form-group">
                        <label for="order_id">Sélectionnez une commande à supprimer:</label>
                        <select class="form-control" id="order_id" name="order_id" required>
                            <?php
                            $query = "SELECT order_id FROM orders";
                            $result = mysqli_query($connection, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value=\"{$row['order_id']}\">{$row['order_id']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger" name="delete_order">Supprimer la commande</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
