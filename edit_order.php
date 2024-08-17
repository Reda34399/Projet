<?php
include 'connection.php';
session_start();

if (!isset($_GET['order_id']) || empty($_GET['order_id']) || $_SESSION['role'] != 'admin') {
    die("Commande non spécifiée.");
}

$order_id = intval($_GET['order_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = mysqli_real_escape_string($connection, $_POST['status']);
    
    $updateQuery = "UPDATE tpdata_orders SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($stmt, "si", $newStatus, $order_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: admin_orders.php");
    exit();
} else {
    $query = "SELECT * FROM tpdata_orders WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Commande</title>
</head>
<body>
    <div class="container">
        <h2>Modifier la Commande #<?php echo htmlspecialchars($order['order_id']); ?></h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="status">Statut de la Commande</label>
                <input type="text" name="status" id="status" value="<?php echo htmlspecialchars($order['status']); ?>" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
