<?php
include 'connection.php';
session_start();
include 'navbar.php';
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM Users WHERE username = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['passworde'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['rolee'];

            if ($_SESSION['role'] == 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: account.php');
            }
            exit();
        } else {
            $error_message = "Mot de pass incorrect";
        }
    } else {
        $error_message = "Le nom d'utilisateur inkonu";
    }

    mysqli_stmt_close($stmt); 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
        }

        .navbar {
            background-color: #f8f9fa;
            padding: 1rem;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: center;
        }

        .card {
            margin-top: 2rem;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Se Connecter</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur:</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe:</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" name="submit">Se Connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
