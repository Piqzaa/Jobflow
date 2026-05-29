<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 class="auth-card__title">Mot de passe oublié</h2>
            <p class="auth-card__subtitle">Entrez votre email pour recevoir un lien de réinitialisation</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert--danger">
                <i class="ri-error-warning-line"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert--success">
                <i class="ri-checkbox-circle-line"></i>
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/forgot-password') ?>" method="POST">
            <?php csrf_field(); ?>

            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <div class="input-group has-icon">
                    <i class="ri-mail-line"></i>
                    <input type="email" name="email" id="email" class="form-control" placeholder="exemple@domaine.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-block btn-lg">
                <span>Envoyer le lien</span>
                <i class="ri-mail-send-line"></i>
            </button>
        </form>

        <div class="auth-card__footer">
            <p>Se souvenir du mot de passe ? <a href="<?= url('/login') ?>" class="auth-card__link">Connexion</a></p>
        </div>
    </div>
</div>
