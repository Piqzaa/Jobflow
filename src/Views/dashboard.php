<main class="container">
    <h1>Tableau de bord</h1>
    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['user_email']) ?></strong> !</p>
    <p>Vous êtes maintenant connecté à votre espace Jobflow.</p>
    
    <div class="actions">
        <a href="<?= url('/logout') ?>" class="btn btn-danger">Se déconnecter</a>
    </div>
