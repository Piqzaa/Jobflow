<div class="stats-grid">
    <div class="card card--stats">
        <h3 class="card__stat-label">TVA Collectée (<?= date('Y') ?>)</h3>
        <p class="card__stat-value"><?= number_format($tvaCollectee, 2, ',', ' ') ?> €</p>
        <p class="card__stat-subtitle">Total facturé aux clients</p>
    </div>

    <div class="card card--stats">
        <h3 class="card__stat-label">TVA Déjà Payée</h3>
        <p class="card__stat-value"><?= number_format($tvaPayee, 2, ',', ' ') ?> €</p>
        <p class="card__stat-subtitle">Total reversé à l'État</p>
    </div>

    <div class="card card--stats">
        <h3 class="card__stat-label">Reste à reverser</h3>
        <p class="card__stat-value"><?= number_format($tvaRestante, 2, ',', ' ') ?> €</p>
        <p class="card__stat-subtitle">Montant en attente</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert--danger">
        <i class="ri-error-warning-line"></i>
        <?= e($error) ?>
    </div>
<?php endif; ?>

<div class="tva-layout">
    <aside class="tva-layout__sidebar">
        <section class="card">
            <h2 class="card__title">
                <i class="ri-add-circle-line"></i>
                Enregistrer un versement
            </h2>
            
            <form action="<?= url('/tva/add') ?>" method="POST" class="tva-form">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="montant" class="form-label">Montant versé (€)</label>
                    <div class="input-group has-icon">
                        <i class="ri-money-euro-circle-line"></i>
                        <input type="number" step="0.01" name="montant" id="montant" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="date_paiement" class="form-label">Date du paiement</label>
                    <div class="input-group has-icon">
                        <i class="ri-calendar-line"></i>
                        <input type="date" name="date_paiement" id="date_paiement" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="periode" class="form-label">Période concernée</label>
                    <div class="input-group has-icon">
                        <i class="ri-time-line"></i>
                        <input type="text" name="periode" id="periode" class="form-control" placeholder="Ex: Trimestre 1 2026">
                    </div>
                </div>
                
                <button type="submit" class="btn--primary btn--block">
                    <i class="ri-save-line"></i>
                    Enregistrer le paiement
                </button>
            </form>
        </section>
    </aside>

    <main class="tva-layout__main">
        <div class="table-container">
            <table class="data-table" id="tva-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Période</th>
                        <th>Montant</th>
                        <th class="tva-table__col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="tva-table__empty-state">
                                    <i class="ri-information-line"></i>
                                    Aucun versement enregistré pour le moment.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td data-label="Date"><?= date('d/m/Y', strtotime($payment['date_paiement'])) ?></td>
                                <td data-label="Période" class="c-nom"><?= htmlspecialchars($payment['periode'] ?? '-') ?></td>
                                <td data-label="Montant"><strong><?= number_format($payment['montant'], 2, ',', ' ') ?> €</strong></td>
                                <td>
                                    <div class="table-actions">
                                        <form action="<?= url('/tva/delete') ?>" method="POST" class="delete-tva">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $payment['id'] ?>">
                                            <button type="submit" class="btn-action btn-action--danger" title="Supprimer">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
