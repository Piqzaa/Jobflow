<section class="auth-form">
    <h1>Connexion</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form action="<?= url('/login') ?>" method="POST">
        <!-- Protection CSRF -->
        <?php csrf_field(); ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit" class="btn">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="<?= url('/register') ?>">S'inscrire</a></p>
</section>
