<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 class="auth-card__title">Créer un compte</h2>
            <p class="auth-card__subtitle">Rejoignez Jobflow et simplifiez votre gestion</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert--danger">
                <i class="ri-error-warning-line" aria-hidden="true"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/register') ?>" method="POST">
            <?php csrf_field(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="prenom" class="form-label">Prénom</label>
                    <div class="input-group">
                        <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Jean" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nom" class="form-label">Nom</label>
                    <div class="input-group">
                        <input type="text" name="nom" id="nom" class="form-control" placeholder="Dupont" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <div class="input-group has-icon">
                    <i class="ri-mail-line" aria-hidden="true"></i>
                    <input type="email" name="email" id="email" class="form-control" placeholder="exemple@domaine.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group has-icon">
                    <i class="ri-lock-2-line" aria-hidden="true"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmation</label>
                <div class="input-group has-icon">
                    <i class="ri-lock-check-line" aria-hidden="true"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn--primary btn--block btn--lg">
                <span>Créer mon compte</span>
                <i class="ri-user-add-line" aria-hidden="true"></i>
            </button>
        </form>

        <div class="auth-card__footer">
            <p>Déjà inscrit ? <a href="<?= url('/login') ?>" class="auth-card__link">Se connecter</a></p>
        </div>
    </div>
</div>


