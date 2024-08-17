<?php
include 'connection.php'; 

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['passworde'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $role = 'client';

        $query = "INSERT INTO Users (username, email, passworde, rolee) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashedPassword, $role);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: signin.php');
            exit();
        } else {
            echo "<div class='alert alert-danger mt-3'>Erreur lors de l'inscription : " . mysqli_error($connection) . "</div>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='alert alert-danger mt-3'>Les mots de passe ne correspondent pas.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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

    <?php include 'navbar.php'; ?> 

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Inscription</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Adresse e-mail:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="passworde">Mot de passe:</label>
                                <input type="password" class="form-control" id="passworde" name="passworde" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmer mot de passe:</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" name="submit">S'inscrire</button>
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
