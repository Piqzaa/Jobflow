<div class="dashboard">
    <?php if ($tvaStatus === 'obligatoire_immediat'): ?>
        <div class="alert alert--danger" id="alert-tva-danger">
            <div class="alert__text">
                <strong>Seuil de TVA dépassé (41 250 €) !</strong><br>
                Facturez la TVA dès maintenant.
            </div>
            <button type="button" class="alert__close">
                &times;
            </button>
        </div>
    <?php elseif ($tvaStatus === 'alerte_prochaine_annee'): ?>
        <div class="alert alert--warning" id="alert-tva-warning">
            <div class="alert__text">
                <strong>Seuil de franchise dépassé (37 500 €)</strong><br>
                Facturation TVA obligatoire dès le 1er janvier prochain.
            </div>
            <button type="button" class="alert__close">
                &times;
            </button>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <article class="card card--stats">
            <div class="card__title">
                Chiffre d'Affaires HT
            </div>
            <p class="card__stat-value"><?= number_format($caTotal, 2, ',', ' ') ?> €</p>
            
            <div class="dashboard__progress">
                <div class="dashboard__progress-header">
                    <span class="dashboard__progress-label">Limite Micro-Entreprise</span>
                    <span class="dashboard__progress-percent"><?= round($percentMicro) ?>%</span>
                </div>
                <div class="dashboard__progress-bar">
                    <div class="dashboard__progress-fill" style="width: <?= $percentMicro ?>%;"></div>
                </div>
                <p class="dashboard__progress-help">Plafond : <?= number_format($seuilMicro, 0, ',', ' ') ?> €</p>
            </div>
        </article>


        <article class="card card--stats">
            <div class="card__title">
                Situation TVA
            </div>
            <p class="card__stat-value"><?= number_format($tvaRestante, 2, ',', ' ') ?> €</p>
            <p class="card__stat-subtitle">Reste à payer à l'État</p>
            
            <div class="dashboard__tva-details">
                <div class="dashboard__tva-row">
                    <span class="dashboard__tva-label">Collectée</span>
                    <strong class="dashboard__tva-value"><?= number_format($tvaCollectee, 2, ',', ' ') ?> €</strong>
                </div>
                <div class="dashboard__tva-row">
                    <span class="dashboard__tva-label">Déjà payée</span>
                    <strong class="dashboard__tva-value dashboard__tva-value--paid"><?= number_format($tvaPayee, 2, ',', ' ') ?> €</strong>
                </div>
            </div>
        </article>
    </div>


    <section class="card chart-card">
        <div class="chart-card__header">
            <h3 class="chart-card__title">Performance Mensuelle</h3>
            <p class="chart-card__subtitle">Évolution du Chiffre d'Affaires HT</p>
        </div>
        <div class="chart-card__container">
            <canvas id="caChart" data-monthly='<?= $monthlyCA ?>'></canvas>
        </div>
    </section>
</div>


