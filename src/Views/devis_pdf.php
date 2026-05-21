<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis <?= htmlspecialchars($devis['numero']) ?></title>
</head>
<body>

    <div class="header">
        <div class="company-info">
            <?php if (!empty($userProfile['logo_filename'])): ?>
                <img src="<?= 'storage/uploads/' . htmlspecialchars($userProfile['logo_filename']) ?>"><br>
            <?php endif; ?>
            <strong><?= htmlspecialchars($userProfile['entreprise'] ?? 'Mon Entreprise') ?></strong><br>
            <?= htmlspecialchars($userProfile['prenom'] ?? '') ?> <?= htmlspecialchars($userProfile['nom'] ?? '') ?><br>
            <?= nl2br(htmlspecialchars($userProfile['adresse'] ?? '')) ?><br>
            <?= htmlspecialchars($userProfile['code_postal'] ?? '') ?> <?= htmlspecialchars($userProfile['ville'] ?? '') ?><br>
            SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?><br>
            <?php if ($devis['montant_tva'] > 0 && !empty($userProfile['tva_intra'])): ?>
                TVA Intra : <?= htmlspecialchars($userProfile['tva_intra']) ?><br>
            <?php endif; ?>
        </div>

        <div class="client-info">
            <strong>À l'attention de :</strong><br>
            <?= htmlspecialchars($devis['client_nom']) ?><br>
            <?= nl2br(htmlspecialchars($devis['client_adresse'] ?? '')) ?><br>
            <?= htmlspecialchars($devis['client_code_postal'] ?? '') ?> <?= htmlspecialchars($devis['client_ville'] ?? '') ?><br>
            <?php if (!empty($devis['client_siret'])): ?>
                SIRET : <?= htmlspecialchars($devis['client_siret']) ?>
            <?php endif; ?>
        </div>
    </div>

    <h1>DEVIS n° <?= htmlspecialchars($devis['numero']) ?></h1>
    <p>
        Date d'émission : <?= date('d/m/Y', strtotime($devis['date_emission'])) ?><br>
        Date de validité : <?= date('d/m/Y', strtotime($devis['date_validite'])) ?>
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
        <div>Total HT : <?= number_format($devis['montant_ht'], 2, ',', ' ') ?> €</div>
        <?php if ($devis['montant_tva'] > 0): ?>
            <div>TVA (20%) : <?= number_format($devis['montant_tva'], 2, ',', ' ') ?> €</div>
        <?php endif; ?>
        <div><strong>TOTAL TTC : <?= number_format($devis['montant_ttc'], 2, ',', ' ') ?> €</strong></div>
    </div>

    <?php if ($devis['montant_tva'] == 0): ?>
        <p class="legal">TVA non applicable, art. 293 B du CGI</p>
    <?php endif; ?>

    <div class="payment-terms">
        <strong>Conditions de paiement :</strong><br>
        - Mode de règlement : Virement bancaire ou Chèque<br>
        - Délai de paiement : 30 jours à réception de la facture<br>
        - Tout retard de paiement donnera lieu à l'application de pénalités de retard au taux légal en vigueur, ainsi qu'à une indemnité forfaitaire pour frais de recouvrement de 40 € (Art. L. 441-6 du Code de commerce).
    </div>

    <?php if (!empty($devis['notes'])): ?>
    <div class="notes">
        <strong>Notes :</strong><br>
        <?= nl2br(htmlspecialchars($devis['notes'])) ?>
    </div>
    <?php endif; ?>

    <div class="signature">
        <div class="signature-box-client">
            <strong>Pour le client :</strong><br>
            Date :<br>
            Mention "Lu et approuvé"<br>
            Signature :
        </div>
    </div>

    <div class="footer">
        <?= htmlspecialchars($userProfile['entreprise'] ?? '') ?> - SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?> - <?= htmlspecialchars($userProfile['ville'] ?? '') ?><br>
        Merci de votre confiance.
    </div>

</body>
</html>
