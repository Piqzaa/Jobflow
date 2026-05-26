<footer class="footer">
    <div class="footer__container">
        <div class="footer__content">
            <div class="footer__brand">
                <a href="<?= url('/') ?>" class="footer__brand-logo">
                    <span class="footer__logo-job">Job</span>
                    <span class="footer__logo-flow">flow</span>
                    <span class="footer__logo-dot"></span>
                </a>
                <p class="footer__description">
                    La solution simple pour gérer vos devis et factures.
                </p>
            </div>

            <div class="footer__socials">
                <a target="_blank" href="https://www.linkedin.com/in/alex-berrel-8b800a399/" class="footer__social-link" aria-label="LinkedIn"><i class="ri-linkedin-box-line"></i></a>
                <a target="_blank" href="https://github.com/piqzaa" class="footer__social-link" aria-label="GitHub"><i class="ri-github-line"></i></a>
            </div>
        </div>

        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?= date('Y') ?> Jobflow. Tout droit réservé. | 
                <a href="<?= url('/mentions-legales') ?>">Mentions Légales</a> | 
                <a href="<?= url('/cgu') ?>">CGU</a> | 
                <a href="<?= url('/rgpd') ?>">Confidentialité</a>
            </p>
        </div>
    </div>
</footer>
