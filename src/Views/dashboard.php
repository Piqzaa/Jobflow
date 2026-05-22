<main class="container">
    <div class="dashboard-header">
        <h1>Tableau de bord - <?= date('Y') ?></h1>
        <p>Résumé de votre activité Chiffre d'Affaires et TVA.</p>
    </div>

    <!-- Alertes TVA -->
    <?php if ($tvaStatus === 'obligatoire_immediat'): ?>
        <div class="alert alert-danger">
            <strong>⚠️ Seuil de TVA dépassé (41 250 €) !</strong><br>
            Vous devez facturer la TVA sur vos prochaines factures dès maintenant.
        </div>
    <?php elseif ($tvaStatus === 'alerte_prochaine_annee'): ?>
        <div class="alert alert-warning">
            <strong>ℹ️ Seuil de franchise dépassé (37 500 €)</strong><br>
            Vous restez en franchise cette année, mais vous devrez facturer la TVA dès le 1er janvier prochain si vous ne dépassez pas 41 250 €.
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <!-- Carte CA Global -->
        <div class="card card-stats">
            <h3>Chiffre d'Affaires HT</h3>
            <p class="stat-value"><?= number_format($caTotal, 2, ',', ' ') ?> €</p>
            
            <div class="progress-container">
                <div class="progress-label">
                    <span>Limite Micro-Entreprise (<?= number_format($seuilMicro, 0, ',', ' ') ?> €)</span>
                    <span><?= round($percentMicro) ?>%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?= $percentMicro ?>%; background-color: <?= $percentMicro > 90 ? '#e74c3c' : '#2ecc71' ?>;"></div>
                </div>
            </div>
        </div>

        <!-- Carte TVA -->
        <div class="card card-stats">
            <h3>TVA Collectée</h3>
            <p class="stat-value"><?= number_format($tvaCollectee, 2, ',', ' ') ?> €</p>
            <p class="stat-subtitle">Total à reverser (selon vos factures payées)</p>
        </div>
    </div>

    <!-- Graphique -->
    <div class="card chart-card">
        <h3>Évolution du CA HT par mois</h3>
        <div style="height: 300px;">
            <canvas id="caChart" data-monthly='<?= $monthlyCA ?>'></canvas>
        </div>
    </div>

</main>
