<section class="auth-form">
    <h1>Mot de passe oublié</h1>

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

    <form action="<?= url('/forgot-password') ?>" method="POST">
        <!-- Protection CSRF -->
        <?php csrf_field(); ?>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <button type="submit" class="btn">Envoyer le lien de réinitialisation</button>
    </form>

    <p>Retour à la <a href="<?= url('/login') ?>">connexion</a></p>
</section>