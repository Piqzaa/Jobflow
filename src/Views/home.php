<section class="home-hero">
    <div class="home-hero__content">
        <h1 class="home-hero__title">
            Gérez 
            votre micro-entreprise
            <span class="text-gradient">sans stress.</span>
        </h1>
        <p class="home-hero__subtitle">
            L'outil tout-en-un pour les freelances : facturation, devis, suivi CA et gestion de la TVA. 
            Simple, rapide et conçu pour votre quotidien.
        </p>
        <div class="home-hero__actions">
            <a href="<?= url('/register') ?>" class="btn--primary btn--hero">S'inscrire gratuitement</a>
            <a href="<?= url('/login') ?>" class="btn--outline btn--hero">Se connecter</a>
        </div>
    </div>
</section>

<section class="home-preview">
    <div class="home-preview__container">
        <div class="home-preview__window">
                <picture>
                    <source media="(max-width: 768px)" srcset="<?= url('/assets/images/home-preview-mobile.jpg') ?>">
                    <img src="<?= url('/assets/images/home-preview.jpg') ?>" alt="Interface Jobflow">
                </picture>
        </div>
    </div>
</section>

<section class="home-features">
    <div class="home-features__grid">
        <div class="card card--feature">
            <div class="card--feature__icon">
                <i class="ri-file-list-3-line"></i>
            </div>
            <h3 class="card--feature__title">Devis & Factures</h3>
            <p class="card--feature__text">Créez des documents professionnels en quelques secondes et suivez leur statut en temps réel.</p>
        </div>

        <div class="card card--feature">
            <div class="card--feature__icon">
                <i class="ri-line-chart-line"></i>
            </div>
            <h3 class="card--feature__title">Suivi du CA</h3>
            <p class="card--feature__text">Visualisez votre progression par rapport aux seuils de la micro-entreprise et gérez votre TVA sereinement.</p>
        </div>

        <div class="card card--feature">
            <div class="card--feature__icon">
                <i class="ri-user-star-line"></i>
            </div>
            <h3 class="card--feature__title">Gestion Clients</h3>
            <p class="card--feature__text">Centralisez toutes les informations de vos clients et accédez à leur historique de documents.</p>
        </div>
    </div>
</section>

<section class="home-cta">
    <div class="home-cta__container">
        <h2 class="home-cta__title">
            Prêt à simplifier 
            <span class="text-gradient">votre gestion ?</span>
        </h2>
        <p class="home-cta__text">Rejoignez les freelances qui utilisent Jobflow pour gagner du temps chaque jour.</p>
        <a href="<?= url('/register') ?>" class="btn--primary btn--hero">Créer mon compte maintenant</a>
    </div>
</section>
