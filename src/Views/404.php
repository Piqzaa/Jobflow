<div class="full-height flex-center">
    <div>
        <h1 class="text-huge text-gradient">404</h1>
        <h2>Oups ! Page introuvable</h2>
        <p>
            La page que vous recherchez semble avoir disparu ou n'a jamais existé.
        </p>
        <div class="mt-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= url('/dashboard') ?>" class="btn-primary">Retour au tableau de bord</a>
            <?php else: ?>
                <a href="<?= url('/') ?>" class="btn-primary">Retour à l'accueil</a>
            <?php endif; ?>
        </div>
    </div>
</div>
