<?php

include 'connection.php';
session_start();

if (isset($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);

    $query = "SELECT o.order_id, o.order_date, o.total_amount, u.username 
              FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);

    if ($order) {
        echo "<div class='container mt-5'>";
        echo "<div class='card shadow-lg'>";
        echo "<div class='card-header bg-info text-white text-center'>";
        echo "<h2>Confirmation de la commande</h2>";
        echo "</div>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Détails de la commande</h5>";
        echo "<p class='card-text'><strong>Numéro de commande :</strong> " . $order['order_id'] . "</p>";
        echo "<p class='card-text'><strong>Date de commande :</strong> " . date('d/m/Y', strtotime($order['order_date'])) . "</p>";
        echo "<p class='card-text'><strong>Montant total :</strong> " . number_format($order['total_amount'], 2) . " €</p>";
        echo "<p class='card-text'><strong>Utilisateur :</strong> " . htmlspecialchars($order['username']) . "</p>";
        
        $itemQuery = "SELECT p.name, oi.quantity, oi.price 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.product_id 
                      WHERE oi.order_id = ?";
        $stmt = mysqli_prepare($connection, $itemQuery);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        $itemsResult = mysqli_stmt_get_result($stmt);

        echo "<h5 class='card-title mt-4'>Articles de la commande</h5>";
        echo "<ul class='list-group'>";
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
            echo htmlspecialchars($item['name']);
            echo "<span class='badge badge-primary badge-pill'>Quantité : " . $item['quantity'] . "</span>";
            echo "<span class='badge badge-success badge-pill'>Prix : " . number_format($item['price'], 2) . " €</span>";
            echo "</li>";
        }
        echo "</ul>";

        echo "</div>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='container mt-5'>";
        echo "<div class='alert alert-danger' role='alert'>";
        echo "Commande introuvable.";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-warning' role='alert'>";
    echo "ID de commande manquant.";
    echo "</div>";
    echo "</div>";
}

mysqli_close($connection);
include 'footer.php';
?>
