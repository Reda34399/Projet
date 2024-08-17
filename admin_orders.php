<?php
include 'connection.php';
session_start();
include 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: signin.php');
    exit();
}


$query_orders = "SELECT order_id, user_id, order_date, total_amount FROM tpdata_orders";
$result_orders = mysqli_query($connection, $query_orders);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestion des Commandes</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Commande</th>
                    <th>ID Utilisateur</th>
                    <th>Date</th>
                    <th>Montant Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result_orders)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                        <td>
                            <a href="edit_order.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-warning">Modifier</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
