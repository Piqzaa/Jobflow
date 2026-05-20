<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis <?= htmlspecialchars($devis['numero']) ?></title>
</head>
<body>

    <div class="container">
        <header>
            <div class="company">
                <h1><?= htmlspecialchars($userProfile['entreprise'] ?? 'Mon Entreprise') ?></h1>
                <p>
                    <?= htmlspecialchars($userProfile['prenom'] ?? '') ?> <?= htmlspecialchars($userProfile['nom'] ?? '') ?><br>
                    <?= htmlspecialchars($userProfile['adresse'] ?? '') ?><br>
                    <?= htmlspecialchars($userProfile['code_postal'] ?? '') ?> <?= htmlspecialchars($userProfile['ville'] ?? '') ?><br>
                    SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?>
                </p>
            </div>

            <div class="client">
                <p><strong>Destinataire :</strong></p>
                <p>
                    <?= htmlspecialchars($devis['client_nom']) ?><br>
                    <?= htmlspecialchars($devis['client_adresse'] ?? '') ?><br>
                    <?= htmlspecialchars($devis['client_code_postal'] ?? '') ?> <?= htmlspecialchars($devis['client_ville'] ?? '') ?>
                </p>
            </div>
        </header>

        <section class="main-info">
            <h2>DEVIS n° <?= htmlspecialchars($devis['numero']) ?></h2>
            <p>Émis le : <?= date('d/m/Y', strtotime($devis['date_emission'])) ?></p>
            <p>Valable jusqu'au : <?= date('d/m/Y', strtotime($devis['date_validite'])) ?></p>
        </section>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Qté</th>
                    <th>PU HT</th>
                    <th>Total HT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['designation']) ?></td>
                    <td><?= number_format($item['quantite'], 0) ?></td>
                    <td><?= number_format($item['prix_unitaire'], 2, ',', ' ') ?> €</td>
                    <td><?= number_format($item['total_ht'], 2, ',', ' ') ?> €</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <footer class="totals">
            <p>Total HT : <?= number_format($devis['montant_ht'], 2, ',', ' ') ?> €</p>
            <p>TVA (20%) : <?= number_format($devis['montant_tva'], 2, ',', ' ') ?> €</p>
            <p><strong>Total TTC : <?= number_format($devis['montant_ttc'], 2, ',', ' ') ?> €</strong></p>
        </footer>

        <?php if (!empty($devis['notes'])): ?>
        <section class="notes">
            <p><strong>Notes :</strong></p>
            <p><?= nl2br(htmlspecialchars($devis['notes'])) ?></p>
        </section>
        <?php endif; ?>
    </div>

</body>
</html>
