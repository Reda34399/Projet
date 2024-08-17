<!-- account.php -->
<?php
include 'connection.php';
session_start();
include 'navbar.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT P.product_id, P.name, P.description, P.price, P.image, C.quantity 
          FROM Cart C 
          JOIN Products P ON C.product_id = P.product_id 
          WHERE C.user_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Mon Panier</h2>
        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $totalPrice += $row['price'] * $row['quantity'];
                    echo "<div class='col-md-4'>";
                    echo "<div class='card mb-4'>";
                    echo "<img src='" . htmlspecialchars($row['image']) . "' class='card-img-top' alt='" . htmlspecialchars($row['name']) . "'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>";
                    echo "<p class='card-text'>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<p class='card-text'>Prix: $" . htmlspecialchars($row['price']) . " x " . htmlspecialchars($row['quantity']) . "</p>";
                    echo "<form method='post' action='remove_from_cart.php' class='d-inline'>";
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id']) . "'>";
                    echo "<button type='submit' class='btn btn-danger mb-2'>Supprimer du panier</button>";
                    echo "</form>";
                    
                    echo "<a href='product_detail.php?id=" . htmlspecialchars($row['product_id']) . "' class='btn btn-info'>Voir les détails</a>";
                    
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Votre panier est vide.</p>";
            }
            ?>
        </div>

        
    </div>

    <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" aria-labelledby="buyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="process_order.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="buyModalLabel">Confirmation de l'Achat</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Vous êtes sur le point d'acheter les articles dans votre panier pour un total de <strong>$<?php echo number_format($totalPrice, 2, ',', ' '); ?></strong>.</p>
                        <div class="form-group">
                            <label for="address">Adresse de Livraison</label>
                            <input type="text" name="address" class="form-control" id="address" required>
                        </div>
                        <div class="form-group">
                            <label for="payment">Méthode de Paiement</label>
                            <select name="payment" class="form-control" id="payment" required>
                                <option value="paypal">PayPal</option>
                                <option value="credit_card">Carte de Crédit</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Confirmer l'Achat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=Ab2V8b_j4IGxokzz73nW8snElGowARQfKt3hFBpRT2AbbmbE6MI_NFAcp_gxip5TfSpJvbbUZORbz4vc"></script>
    <script>
    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo number_format($totalPrice, 2, '.', ''); ?>' 
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                alert('Transaction complétée par ' + details.payer.name.given_name + '!');
            });
        },
        onError: function(err) {
            console.error('Erreur dans le paiement', err);
            alert('Le paiement a échoué.');
        }
    }).render('#paypal-button-container');
    </script>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
