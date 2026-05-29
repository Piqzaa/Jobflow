<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis <?= e($devis['numero']) ?></title>
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
                            <h2 class="pdf-header__company-name"><?= htmlspecialchars($userProfile['entreprise'] ?? 'Mon Entreprise') ?></h2>
                            <p>
                                <strong><?= htmlspecialchars($userProfile['prenom'] ?? '') ?> <?= htmlspecialchars($userProfile['nom'] ?? '') ?></strong><br>
                                <?= nl2br(htmlspecialchars($userProfile['adresse'] ?? '')) ?><br>
                                <?= htmlspecialchars($userProfile['code_postal'] ?? '') ?> <?= htmlspecialchars($userProfile['ville'] ?? '') ?>
                            </p>
                            <p>
                                SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?>
                                <?php if ($devis['montant_tva'] > 0 && !empty($userProfile['tva_intra'])): ?>
                                    <br>TVA Intra : <?= htmlspecialchars($userProfile['tva_intra']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </td>
                    <td class="pdf-header__client">
                        <div class="pdf-header__client-box">
                            <p class="pdf-header__client-label">Adressé à</p>
                            <h3 class="pdf-header__client-name"><?= htmlspecialchars($devis['client_nom']) ?></h3>
                            <div class="pdf-header__client-details">
                                <?= nl2br(htmlspecialchars($devis['client_adresse'] ?? '')) ?><br>
                                <?= htmlspecialchars($devis['client_code_postal'] ?? '') ?> <?= htmlspecialchars($devis['client_ville'] ?? '') ?>
                                <?php if (!empty($devis['client_siret'])): ?>
                                    <br>SIRET : <?= htmlspecialchars($devis['client_siret']) ?>
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
                        <h1 class="pdf-title-box__title">DEVIS</h1>
                        <p class="pdf-title-box__number">N° <?= htmlspecialchars($devis['numero']) ?></p>
                    </td>
                    <td class="pdf-title-box__meta">
                        <p>Date d'émission : <?= date('d/m/Y', strtotime($devis['date_emission'])) ?></p>
                        <p>Date de validité : <?= date('d/m/Y', strtotime($devis['date_validite'])) ?></p>
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
                        <td class="pdf-table__td"><?= htmlspecialchars($item['designation']) ?></td>
                        <td class="pdf-table__td pdf-table__td--center"><?= number_format($item['quantite'], 2, ',', ' ') ?></td>
                        <td class="pdf-table__td pdf-table__td--right"><?= number_format($item['prix_unitaire'], 2, ',', ' ') ?> €</td>
                        <td class="pdf-table__td pdf-table__td--right"><?= number_format($item['total_ht'], 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- TOTALS (Utilisation d'une table pour forcer l'affichage à droite proprement) -->
            <div class="pdf-totals-container">
                <table class="pdf-totals-table">
                    <tr>
                        <td class="pdf-totals__label">Total HT</td>
                        <td class="pdf-totals__value"><?= number_format($devis['montant_ht'], 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php if ($devis['montant_tva'] > 0): ?>
                    <tr>
                        <td class="pdf-totals__label">TVA (20%)</td>
                        <td class="pdf-totals__value"><?= number_format($devis['montant_tva'], 2, ',', ' ') ?> €</td>
                    </tr>
                    <?php endif; ?>
                    <tr class="pdf-totals__row--grand-total">
                        <td class="pdf-totals__label">TOTAL TTC</td>
                        <td class="pdf-totals__value"><?= number_format($devis['montant_ttc'], 2, ',', ' ') ?> €</td>
                    </tr>
                </table>
            </div>

            <?php if ($devis['montant_tva'] == 0): ?>
                <p class="pdf-legal">TVA non applicable, art. 293 B du CGI</p>
            <?php endif; ?>

            <div class="pdf-infos">
                <div class="pdf-infos__section">
                    <h4 class="pdf-infos__title">Conditions de règlement</h4>
                    <p class="pdf-infos__text">
                        Règlement par virement bancaire ou chèque.<br>
                        Un acompte de 30% peut être demandé à l'acceptation du devis.
                    </p>
                </div>

                <?php if (!empty($devis['notes'])): ?>
                <div class="pdf-infos__section">
                    <h4 class="pdf-infos__title">Notes</h4>
                    <p class="pdf-infos__text"><?= nl2br(htmlspecialchars($devis['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <table class="pdf-signature-table">
                <tr>
                    <td>
                        <div class="pdf-signature-box">
                            <p class="pdf-signature__title">Bon pour accord</p>
                            <p class="pdf-signature__text pdf-signature__text--small">Date : </p>
                            <p class="pdf-signature__text pdf-signature__text--small pdf-signature__text--italic">Mention "Lu et approuvé"</p>
                            <div class="pdf-signature__area"></div>
                        </div>
                    </td>
                </tr>
            </table>

        </main>

        <footer class="pdf-footer">
            <p class="pdf-footer__text">
                <?= htmlspecialchars($userProfile['entreprise'] ?? '') ?> - SIRET : <?= htmlspecialchars($userProfile['siret'] ?? '') ?><br>
                <?= htmlspecialchars($userProfile['adresse'] ?? '') ?> <?= htmlspecialchars($userProfile['code_postal'] ?? '') ?> <?= htmlspecialchars($userProfile['ville'] ?? '') ?>
            </p>
        </footer>
    </div>

</body>
</html>
