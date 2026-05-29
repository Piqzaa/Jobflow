<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= e($facture['numero']) ?></title>
    <style>
        <?= file_get_contents(__DIR__ . '/../../public/assets/css/main.css') ?>
    </style>
</head>
<body class="pdf-body">

    <div class="pdf-document">
        <!-- HEADER -->
        <header class="pdf-header">
            <table class="pdf-header__table">
                <tr>
                    <td class="pdf-header__company">
                        <?php if (!empty($userProfile['logo_filename'])): ?>
                            <?php $logoPath = realpath(__DIR__ . '/../../public/uploads/logos/' . $userProfile['logo_filename']); ?>
                            <?php if ($logoPath): ?>
                                <img src="<?= $logoPath ?>" class="pdf-header__logo">
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="pdf-header__company-details">
                            <h2 class="pdf-header__company-name"><?= e($userProfile['entreprise'] ?? 'Mon Entreprise') ?></h2>
                            <p>
                                <strong><?= e($userProfile['prenom'] ?? '') ?> <?= e($userProfile['nom'] ?? '') ?></strong><br>
                                <?= nl2br(e($userProfile['adresse'] ?? '')) ?><br>
                                <?= e($userProfile['code_postal'] ?? '') ?> <?= e($userProfile['ville'] ?? '') ?>
                            </p>
                            <p>
                                SIRET : <?= e($userProfile['siret'] ?? '') ?>
                                <?php if ($facture['montant_tva'] > 0 && !empty($userProfile['tva_intra'])): ?>
                                    <br>TVA Intra : <?= e($userProfile['tva_intra']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </td>
                    <td class="pdf-header__client">
                        <div class="pdf-header__client-box">
                            <p class="pdf-header__client-label">Facturé à</p>
                            <h3 class="pdf-header__client-name"><?= e($facture['client_nom']) ?></h3>
                            <div class="pdf-header__client-details">
                                <?= nl2br(e($facture['client_adresse'] ?? '')) ?><br>
                                <?= e($facture['client_code_postal'] ?? '') ?> <?= e($facture['client_ville'] ?? '') ?>
                                <?php if (!empty($facture['client_siret'])): ?>
                                    <br>SIRET : <?= e($facture['client_siret']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </header>

        <!-- TITLE BOX -->
        <section class="pdf-title-box">
            <table class="pdf-title-box__table">
                <tr>
                    <td>
                        <h1 class="pdf-title-box__title">FACTURE</h1>
                        <p class="pdf-title-box__number">N° <?= e($facture['numero']) ?></p>
                    </td>
                    <td class="pdf-title-box__meta">
                        <p>Date d'émission : <?= date('d/m/Y', strtotime($facture['date_emission'])) ?></p>
                        <p>Date d'échéance : <strong class="pdf-text--danger"><?= date('d/m/Y', strtotime($facture['date_echeance'])) ?></strong></p>
                    </td>
                </tr>
            </table>
        </section>

        <!-- ARTICLES TABLE -->
        <main class="pdf-content">
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th class="pdf-table__th">Désignation</th>
                        <th class="pdf-table__th pdf-table__th--qty">Qté</th>
                        <th class="pdf-table__th pdf-table__th--price">PU HT</th>
                        <th class="pdf-table__th pdf-table__th--total">Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                    <tr class="pdf-table__tr <?= $index % 2 === 1 ? 'pdf-table__tr--even' : '' ?>">
                        <td class="pdf-table__td"><?= e($item['designation']) ?></td>
                        <td class="pdf-table__td pdf-table__td--center"><?= number_format($item['quantite'], 2, ',', ' ') ?></td>
                        <td class="pdf-table__td pdf-table__td--right"><?= number_format($item['prix_unitaire'], 2, ',', ' ') ?> €</td>
                        <td class="pdf-table__td pdf-table__td--right"><?= number_format($item['total_ht'], 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- TOTALS -->
            <div class="pdf-totals-container">
                <table class="pdf-totals-table">
                    <tr>
                        <td class="pdf-totals__label">Total HT</td>
                        <td class="pdf-totals__value"><?= number_format($facture['montant_ht'], 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php if ($facture['montant_tva'] > 0): ?>
                    <tr>
                        <td class="pdf-totals__label">TVA (20%)</td>
                        <td class="pdf-totals__value"><?= number_format($facture['montant_tva'], 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php endif; ?>
                    <tr class="pdf-totals__row--grand-total">
                        <td class="pdf-totals__label">NET À PAYER</td>
                        <td class="pdf-totals__value"><?= number_format($facture['montant_ttc'], 2, ',', ' ') ?> €</td>
                    </tr>
                </table>
            </div>

            <?php if ($facture['montant_tva'] == 0): ?>
                <p class="pdf-legal">TVA non applicable, art. 293 B du CGI</p>
            <?php endif; ?>

            <div class="pdf-infos">
                <div class="pdf-infos__section">
                    <h4 class="pdf-infos__title">Informations de paiement</h4>
                    <p class="pdf-infos__text">
                        Mode de règlement : Virement bancaire ou Chèque.<br>
                        <?php if (!empty($userProfile['iban'])): ?>
                            <strong>IBAN :</strong> <?= e($userProfile['iban']) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($userProfile['bic'])): ?>
                            <strong>BIC :</strong> <?= e($userProfile['bic']) ?><br>
                        <?php endif; ?>
                        Échéance : <span class="pdf-text--bold"><?= date('d/m/Y', strtotime($facture['date_echeance'])) ?></span>
                    </p>
                </div>
                            </br>
                <div class="pdf-infos__section">
                    <h4 class="pdf-infos__title">Mentions Légales (Retard de paiement)</h4>
                    <p class="pdf-infos__text">
                        En cas de retard de paiement, des pénalités de retard au taux légal en vigueur seront appliquées sur le montant TTC. 
                        De plus, une indemnité forfaitaire pour frais de recouvrement de 40 € sera due (Art. L. 441-10 du Code de commerce).
                    </p>
                </div>

                <?php if (!empty($facture['notes'])): ?>
                <div class="pdf-infos__section">
                    <h4 class="pdf-infos__title">Notes</h4>
                    <p class="pdf-infos__text"><?= nl2br(e($facture['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </main>

        <footer class="pdf-footer">
            <p class="pdf-footer__text">
                <?= e($userProfile['entreprise'] ?? '') ?> - SIRET : <?= e($userProfile['siret'] ?? '') ?><br>
                <?= e($userProfile['adresse'] ?? '') ?> <?= e($userProfile['code_postal'] ?? '') ?> <?= e($userProfile['ville'] ?? '') ?>
            </p>
        </footer>
    </div>

</body>
</html>
