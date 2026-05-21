<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= htmlspecialchars($facture['numero']) ?></title>
</head>
<body>

    <div class="header">
        <div class="company-info">
            <?php if (!empty($userProfile['logo_filename'])): ?>
                <?php $logoPath = realpath(__DIR__ . '/../../public/uploads/logos/' . $userProfile['logo_filename']); ?>
                <?php if ($logoPath): ?>
                    <img src="<?= $logoPath ?>" width="150"><br>
                <?php endif; ?>
            <?php endif; ?>
            <strong><?= htmlspecialchars($userProfile['entreprise'] ?? 'Mon Entreprise') ?></strong><br>
            <?= htmlspecialchars($userProfile['prenom'] ?? '') ?> <?= htmlspecialchars($userProfile['nom'] ?? '') ?><br>
            <?= nl2br(htmlspecialchars($userProfile['adresse'] ?? '')) ?><br>
            <?= htmlspecialchars($userProfile['code_postal'] ?? '') ?> <?= htmlspecialchars($userProfile['ville'] ?? '') ?><br>
            SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?><br>
            <?php if ($facture['montant_tva'] > 0 && !empty($userProfile['tva_intra'])): ?>
                TVA Intra : <?= htmlspecialchars($userProfile['tva_intra']) ?><br>
            <?php endif; ?>
        </div>

        <div class="client-info">
            <strong>À l'attention de :</strong><br>
            <?= htmlspecialchars($facture['client_nom']) ?><br>
            <?= nl2br(htmlspecialchars($facture['client_adresse'] ?? '')) ?><br>
            <?= htmlspecialchars($facture['client_code_postal'] ?? '') ?> <?= htmlspecialchars($facture['client_ville'] ?? '') ?><br>
            <?php if (!empty($facture['client_siret'])): ?>
                SIRET : <?= htmlspecialchars($facture['client_siret']) ?>
            <?php endif; ?>
        </div>
    </div>

    <h1>FACTURE n° <?= htmlspecialchars($facture['numero']) ?></h1>
    <p>
        Date de facturation : <?= date('d/m/Y', strtotime($facture['date_emission'])) ?><br>
        Date d'échéance : <strong><?= date('d/m/Y', strtotime($facture['date_echeance'])) ?></strong>
    </p>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Qté</th>
                <th>Prix Unitaire HT</th>
                <th>Total HT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['designation']) ?></td>
                <td><?= number_format($item['quantite'], 2, ',', ' ') ?></td>
                <td><?= number_format($item['prix_unitaire'], 2, ',', ' ') ?> €</td>
                <td><?= number_format($item['total_ht'], 2, ',', ' ') ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div>Total HT : <?= number_format($facture['montant_ht'], 2, ',', ' ') ?> €</div>
        <?php if ($facture['montant_tva'] > 0): ?>
            <div>TVA (20%) : <?= number_format($facture['montant_tva'], 2, ',', ' ') ?> €</div>
        <?php endif; ?>
        <div><strong>TOTAL TTC : <?= number_format($facture['montant_ttc'], 2, ',', ' ') ?> €</strong></div>
    </div>

    <?php if ($facture['montant_tva'] == 0): ?>
        <p class="legal">TVA non applicable, art. 293 B du CGI</p>
    <?php endif; ?>

    <div class="payment-terms">
        <strong>Conditions de paiement :</strong><br>
        - Mode de règlement : Virement bancaire ou Chèque<br>
        <?php if (!empty($userProfile['iban'])): ?>
            - IBAN : <?= htmlspecialchars($userProfile['iban']) ?><br>
        <?php endif; ?>
        <?php if (!empty($userProfile['bic'])): ?>
            - BIC : <?= htmlspecialchars($userProfile['bic']) ?><br>
        <?php endif; ?>
        - Échéance : Le <?= date('d/m/Y', strtotime($facture['date_echeance'])) ?><br>
        - Tout retard de paiement donnera lieu à l'application de pénalités de retard au taux légal en vigueur, ainsi qu'à une indemnité forfaitaire pour frais de recouvrement de 40 € (Art. L. 441-6 du Code de commerce).
    </div>

    <?php if (!empty($facture['notes'])): ?>
    <div class="notes">
        <strong>Notes :</strong><br>
        <?= nl2br(htmlspecialchars($facture['notes'])) ?>
    </div>
    <?php endif; ?>

    <div class="footer">
        <?= htmlspecialchars($userProfile['entreprise'] ?? '') ?> - SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?> - <?= htmlspecialchars($userProfile['ville'] ?? '') ?><br>
        Merci de votre confiance.
    </div>

</body>
</html>
