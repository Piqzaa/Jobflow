<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/partials/head.php'; ?>
<body class="<?= isset($_SESSION['user_id']) ? 'app-logged-in' : 'app-public' ?>">

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="app-layout">
            <aside class="sidebar">
                <div class="sidebar__brand">
                    <a href="<?= url('/dashboard') ?>">
                        <div class="sidebar__brand-logo">
                            <span class="sidebar__brand-job">Job</span>
                            <span class="sidebar__brand-flow">flow</span>
                        </div>
                        <span class="sidebar__brand-dot"></span>
                    </a>
                </div>

                <nav class="sidebar__nav">
                    <span class="sidebar__section-label">Navigation</span>
                    <ul class="sidebar__list">
                        <li class="sidebar__item">
                            <a href="<?= url('/dashboard') ?>" class="sidebar__link <?= $uri === '/dashboard' ? 'sidebar__link--active' : '' ?>">
                                <i class="ri-dashboard-line"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a href="<?= url('/clients') ?>" class="sidebar__link <?= str_contains($uri, '/clients') ? 'sidebar__link--active' : '' ?>">
                                <i class="ri-group-line"></i> Clients
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a href="<?= url('/devis') ?>" class="sidebar__link <?= str_contains($uri, '/devis') ? 'sidebar__link--active' : '' ?>">
                                <i class="ri-file-text-line"></i> Devis
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a href="<?= url('/factures') ?>" class="sidebar__link <?= str_contains($uri, '/factures') ? 'sidebar__link--active' : '' ?>">
                                <i class="ri-receipt-line"></i> Factures
                            </a>
                        </li>
                    </ul>
                    <ul class="sidebar__list">
                        <li class="sidebar__item">
                            <a href="<?= url('/tva') ?>" class="sidebar__link <?= $uri === '/tva' ? 'sidebar__link--active' : '' ?>">
                                <i class="ri-percent-line"></i> Gestion TVA
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="sidebar__footer">
                    <a href="<?= url('/logout') ?>" class="sidebar__link sidebar__link--logout">
                        <i class="ri-logout-circle-line"></i> Déconnexion
                    </a>

                    <a href="<?= url('/profile') ?>" class="sidebar__user-card <?= $uri === '/profile' ? 'sidebar__user-card--active' : '' ?>">
                        <div class="sidebar__user-avatar">
                            <?= strtoupper(substr($_SESSION['user_prenom'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="sidebar__user-info">
                            <span class="sidebar__user-name"><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'U') ?></span>
                            <span class="sidebar__user-sub">Mon profil</span>
                        </div>
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </aside>

            <div class="app-main">
                <header class="topbar">
                    <h1 class="topbar__title"><?= $title ?? 'Jobflow' ?></h1>
                </header>

                <main class="app-content">
                    <?= $content ?>
                </main>
            </div>
        </div>

    <?php else: ?>
        <?php require __DIR__ . '/partials/header.php'; ?>
        <main class="public-container">
            <?= $content ?>
        </main>
    <?php endif; ?>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>