<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 class="auth-card__title">Nouveau mot de passe</h2>
            <p class="auth-card__subtitle">Choisissez un mot de passe sécurisé pour votre compte</p>
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

        <form action="<?= url('/reset-password') ?>" method="POST">
            <?php csrf_field(); ?>

            <input type="hidden" name="token" value="<?= e($token) ?>">

            <div class="form-group">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <div class="input-group has-icon">
                    <i class="ri-lock-2-line"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmation</label>
                <div class="input-group has-icon">
                    <i class="ri-lock-check-line"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-block btn-lg">
                <span>Enregistrer le mot de passe</span>
                <i class="ri-checkbox-circle-line"></i>
            </button>
        </form>

        <div class="auth-card__footer">
            <p>Retour à la <a href="<?= url('/login') ?>" class="auth-card__link">connexion</a></p>
        </div>
    </div>
</div>
