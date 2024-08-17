<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-img-top {
            width: 100%;
            height: 600px; 
            object-fit: cover; 
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <?php
            include 'connection.php';

            $query_categories = "SELECT * FROM Categories";
            $result_categories = mysqli_query($connection, $query_categories); 

            while ($category = mysqli_fetch_assoc($result_categories)): 
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <?php if (!empty($category['image'])): ?>  
                            <img src="<?php echo $category['image']; ?>" class="card-img-top" alt="<?php echo $category['name']; ?>">
                        <?php else: ?>
                            <img src="placeholder-image.jpg" class="card-img-top" alt="Placeholder">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $category['name']; ?></h5>
                            <p class="card-text"><?php echo $category['description']; ?></p>
                            <a href="product.php?category_id=<?php echo $category['category_id']; ?>" class="btn btn-primary">Afficher les produits</a>
                        </div>
                    </div>
                </div>
            <?php endwhile;  ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
