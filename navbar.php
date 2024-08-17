<?php
include 'connection.php';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">MonSite</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>


                    <?php else: ?>

                        <li class="nav-item"><a class="nav-link" href="account.php">Mon compte</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php">Acceuil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>

                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                    <li class="nav-item"><a class="nav-link" href="signin.php">Se Connecter</a></li>
                    <li class="nav-item"><a class="nav-link" href="inscription.php">S'inscrire</a></li>
                    <li class="nav-item"><a class="nav-link" href="signin.php">Panier</a></li> 
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
