<main class="container">
    <h1>Gestion de la TVA</h1>
    <p>Suivez vos versements de TVA effectués à l'État.</p>

    <div class="stats-summary">
        <div class="stats-item">
            <h3>TVA Collectée (<?= date('Y') ?>)</h3>
            <p><?= number_format($tvaCollectee, 2, ',', ' ') ?> €</p>
        </div>
        <div class="stats-item">
            <h3>TVA Déjà Payée</h3>
            <p><?= number_format($tvaPayee, 2, ',', ' ') ?> €</p>
        </div>
        <div class="stats-item">
            <h3>Reste à reverser</h3>
            <p><?= number_format($tvaRestante, 2, ',', ' ') ?> €</p>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="card">
        <h2>Enregistrer un nouveau versement</h2>
        <form action="<?= url('/tva/add') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="montant">Montant versé (€)</label>
                <input type="number" step="0.01" name="montant" id="montant" required>
            </div>
            <div class="form-group">
                <label for="date_paiement">Date du paiement</label>
                <input type="date" name="date_paiement" id="date_paiement" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label for="periode">Période concernée (ex: Trimestre 1 2026)</label>
                <input type="text" name="periode" id="periode" placeholder="Ex: Q1 2026">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
        </form>
    </section>

    <section class="card" style="margin-top: 20px;">
        <h2>Historique des versements</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Période</th>
                    <th>Montant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="4">Aucun versement enregistré.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($payment['date_paiement'])) ?></td>
                            <td><?= htmlspecialchars($payment['periode'] ?? '-') ?></td>
                            <td><?= number_format($payment['montant'], 2, ',', ' ') ?> €</td>
                            <td>
                                <form action="<?= url('/tva/delete') ?>" method="POST" onsubmit="return confirm('Supprimer ce versement ?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $payment['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
