<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 class="auth-card__title">Bon retour !</h2>
            <p class="auth-card__subtitle">Connectez-vous à votre espace Jobflow</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert--danger">
                <i class="ri-error-warning-line"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert--success">
                <i class="ri-checkbox-circle-line"></i>
                <?= e($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/login') ?>" method="POST">
            <?php csrf_field(); ?>

            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <div class="input-group has-icon">
                    <i class="ri-mail-line"></i>
                    <input type="email" name="email" id="email" class="form-control" placeholder="exemple@domaine.com" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group has-icon">
                    <i class="ri-lock-2-line"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="auth-card__helpers">
                <a href="<?= url('/forgot-password') ?>" class="auth-card__link">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-primary btn-block btn-lg">
                <span>Se connecter</span>
                <i class="ri-arrow-right-line"></i>
            </button>
        </form>

        <div class="auth-card__footer">
            <p>Pas encore de compte ? <a href="<?= url('/register') ?>" class="auth-card__link">Créer un compte</a></p>
        </div>
    </div>
</div>
