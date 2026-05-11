<section class="auth-form">
    <h1>Inscription</h1>

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

    <form action="<?= url('/register') ?>" method="POST">
        <?php csrf_field(); ?>

        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" required>
        </div>
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" required>
        </div>
        <div class="form-group">
            <label for="entreprise">Entreprise</label>
            <input type="text" name="entreprise" id="entreprise">
        </div>
        <div class="form-group">
            <label for="siret">SIRET</label>
            <input type="text" name="siret" id="siret">
        </div>
        <div class="form-group">
            <label for="adresse">Adresse</label>
            <input type="text" name="adresse" id="adresse">
        </div>
        <div class="form-group">
            <label for="code_postal">Code postal</label>
            <input type="text" name="code_postal" id="code_postal">
        </div>
        <div class="form-group">
            <label for="ville">Ville</label>
            <input type="text" name="ville" id="ville">
        </div>
        <div class="form-group">
            <label for="telephone">Telephone</label>
            <input type="text" name="telephone" id="telephone">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button type="submit" class="btn">S'inscrire</button>
    </form>

    <p>Vous avez deja un compte ? <a href="<?= url('/login') ?>">Se connecter</a></p>
</section>