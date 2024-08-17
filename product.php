<?php
include 'connection.php'; 

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']); 

    $query_products = "SELECT * FROM Products WHERE category_id = ?";
    $stmt = mysqli_prepare($connection, $query_products);
    mysqli_stmt_bind_param($stmt, "i", $categoryId); 
    mysqli_stmt_execute($stmt);
    $result_products = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result_products) > 0) {
        ?>
        <!DOCTYPE html>
        <html lang="ar">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Products</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        </head>
        <body>
            <?php include 'navbar.php'; ?> 

            <div class="container mt-4">
                <h2 class="mb-4">Products in Category: 
                    <?php 
                    $category_name_query = "SELECT name FROM Categories WHERE category_id = ?";
                    $stmt_cat = mysqli_prepare($connection, $category_name_query);
                    mysqli_stmt_bind_param($stmt_cat, "i", $categoryId);
                    mysqli_stmt_execute($stmt_cat);
                    $result_cat_name = mysqli_stmt_get_result($stmt_cat);
                    $category_name_row = mysqli_fetch_assoc($result_cat_name);
                    echo htmlspecialchars($category_name_row['name']); 
                    ?>
                </h2>

                <div class="row">
                <?php while ($product = mysqli_fetch_assoc($result_products)): ?>
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="card-text">Prix: €<?php echo number_format(htmlspecialchars($product['price']), 2, ',', ' '); ?></p>
                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-info">Voir les détails</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>

                </div>
            </div>

            <?php include 'footer.php'; ?>
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>

        <?php
    } else {
        echo "<div class='alert alert-info'>No products found in this category.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid or missing category ID.</div>";
}
?>
