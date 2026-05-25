<header class="header">
    <div class="header__container">
        <div class="header__brand">
            <a href="<?= url('/') ?>">
                <div class="header__logo">
                    <span class="header__logo-job">Job</span>
                    <span class="header__logo-flow">flow</span>
                </div>
                <span class="header__logo-dot"></span>
            </a>
        </div>

        <button class="header__toggle" aria-label="Menu">
            <i class="ri-menu-3-line"></i>
        </button>

        <nav class="header__nav">
            <div class="header__auth">
                <a href="<?= url('/login') ?>" class="btn-light">Connexion</a>
                <a href="<?= url('/register') ?>" class="btn-primary">Inscription</a>
            </div>
        </nav>
    </div>
</header>
