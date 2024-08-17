<?php
include 'connection.php';
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Produit non spécifié.");
}

$productId = intval($_GET['id']);

$query = "SELECT * FROM Products WHERE product_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $productName = $row['name'];
    $productDescription = $row['description'];
    $productImage = $row['image'];
    $productPrice = $row['price'];
} else {
    die("Produit non trouvé.");
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .product-details {
            margin-top: 50px;
        }

        .product-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product-card h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .product-price {
            font-size: 1.5rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .product-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container product-details">
        <div class="row">
            <div class="col-md-6">
                <div class="product-card">
                    <h2 class="mb-4"><?php echo htmlspecialchars($productName); ?></h2>
                    <img src="<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($productName); ?>" class="product-image">
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-card">
                    <?php if (!empty($productPrice)): ?>
                        <p class="product-price">€<?php echo number_format(htmlspecialchars($productPrice), 2, ',', ' '); ?></p>
                    <?php else: ?>
                        <p class="product-price">Prix non disponible</p>
                    <?php endif; ?>
                    <p class="product-description"><strong>Description:</strong> <?php echo htmlspecialchars($productDescription); ?></p>
                    <form method="post" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#buyModal">Acheter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" aria-labelledby="buyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buyModalLabel">Informations de Commande</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="purchaseForm" method="post" action="process_order.php">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <div class="form-group">
                            <label for="quantity">Quantité</label>
                            <input type="number" name="quantity" class="form-control" id="quantity" value="1" required>
                        </div>
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
                        <div id="paypal-button-container"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary" form="purchaseForm">Confirmer l'Achat</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=Ab2V8b_j4IGxokzz73nW8snElGowARQfKt3hFBpRT2AbbmbE6MI_NFAcp_gxip5TfSpJvbbUZORbz4vc"></script>
    <script>
        var productPrice = <?php echo number_format($productPrice, 2, '.', ''); ?>;
        
        function renderPaypalButton() {
            var container = document.getElementById('paypal-button-container');
            container.innerHTML = '';
            
            var quantity = document.getElementById('quantity').value;
            var totalAmount = productPrice * quantity;
            
            paypal.Buttons({
                createOrder: function (data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: totalAmount.toFixed(2) 
                            }
                        }]
                    });
                },
                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (details) {
                        alert('Transaction complétée par ' + details.payer.name.given_name + '!');
                    });
                },
                onError: function(err){
                    console.log("erreur dans le paiement", err);
                    alert("paiement échoué");
                }
            }).render('#paypal-button-container');
        }

        document.getElementById('quantity').addEventListener('input', function () {
            renderPaypalButton(); 
        });

        $('#buyModal').on('shown.bs.modal', function () {
            renderPaypalButton();
        });
    </script>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
