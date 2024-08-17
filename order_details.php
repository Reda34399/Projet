<!-- order_details.php -->
<?php
include 'connection.php';
session_start();
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Commande non spécifiée.");
}

$order_id = intval($_GET['order_id']);

$query_order = "SELECT order_id, order_date, total_amount FROM tpdata_orders WHERE order_id = ?";
$stmt = mysqli_prepare($connection, $query_order);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result_order = mysqli_stmt_get_result($stmt);

$order = mysqli_fetch_assoc($result_order);

$query_items = "SELECT product_id, quantity, price FROM tpdata_order_items WHERE order_id = ?";
$stmt = mysqli_prepare($connection, $query_items);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result_items = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Détails de la Commande #<?php echo htmlspecialchars($order['order_id']); ?></h2>
        <p>Date de la commande: <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p>Total: €<?php echo htmlspecialchars($order['total_amount']); ?></p>
        <h3>Articles de la commande</h3>
        <div class="row">
            <?php
            while ($item = mysqli_fetch_assoc($result_items)) {
                $query_product = "SELECT name, image FROM tpdata_products WHERE product_id = ?";
                $stmt_product = mysqli_prepare($connection, $query_product);
                mysqli_stmt_bind_param($stmt_product, "i", $item['product_id']);
                mysqli_stmt_execute($stmt_product);
                $result_product = mysqli_stmt_get_result($stmt_product);
                $product = mysqli_fetch_assoc($result_product);

                echo "<div class='col-md-4'>";
                echo "<div class='card mb-4'>";
                echo "<img src='" . htmlspecialchars($product['image']) . "' class='card-img-top' alt='" . htmlspecialchars($product['name']) . "'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($product['name']) . "</h5>";
                echo "<p class='card-text'>Quantité: " . htmlspecialchars($item['quantity']) . "</p>";
                echo "<p class='card-text'>Prix: €" . htmlspecialchars($item['price']) . "</p>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
        <a href="account.php" class="btn btn-secondary">Retour à Mon Compte</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
