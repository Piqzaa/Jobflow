<header class="page-header">
    <div class="page-actions">
        <button class="btn--primary" id="add-facture" data-modal-target="#modal-facture">
            <i class="ri-add-line" aria-hidden="true"></i>
            <span>Nouvelle facture</span>
        </button>
    </div>
</header>

<div class="table-container">
    <table class="data-table" id="factures-table"
        data-add-url="<?= url('/facture/add') ?>"
        data-delete-url="<?= url('/facture/delete') ?>"
        data-get-url="<?= url('/facture/get') ?>"
        data-update-url="<?= url('/facture/update') ?>"
        data-status-url="<?= url('/facture/status') ?>"
    >
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Échéance</th>
                <th>Montant TTC</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures as $f): ?>
            <tr data-id="<?= $f['id'] ?>">
                <td class="f-numero" data-label="Numéro">
                    <span class="text-main"><?= e($f['numero']) ?></span>
                </td>
                <td class="f-client" data-label="Client">
                    <span class="text-main"><?= e($f['client_nom']) ?></span>
                </td>
                <td class="f-date-echeance" data-label="Échéance">
                    <span class="text-sub"><?= date('d/m/Y', strtotime($f['date_echeance'])) ?></span>
                </td>
                <td class="f-montant-ttc" data-label="Montant TTC">
                    <span class="text-main"><?= number_format($f['montant_ttc'], 2, ',', ' ') ?> €</span>
                </td>
                <td class="f-statut" data-label="Statut">
                    <select class="status-select badge-select badge--<?= $f['statut'] ?>" data-id="<?= $f['id'] ?>">
                        <option value="brouillon" <?= $f['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="envoyee" <?= $f['statut'] === 'envoyee' ? 'selected' : '' ?>>Envoyée</option>
                        <option value="payee" <?= $f['statut'] === 'payee' ? 'selected' : '' ?>>Payée</option>
                        <option value="annulee" <?= $f['statut'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn-action view-pdf-btn" data-id="<?= $f['id'] ?>" title="Voir PDF" aria-label="Voir PDF">
                            <i class="ri-file-pdf-line" aria-hidden="true" aria-hidden="true"></i>
                        </button>
                        <button class="btn-action edit-btn <?= $f['statut'] !== 'brouillon' ? 'is-hidden' : '' ?>" data-id="<?= $f['id'] ?>" title="Modifier" aria-label="Modifier">
                            <i class="ri-pencil-line" aria-hidden="true" aria-hidden="true"></i>
                        </button>
                        <button class="btn-action btn-action--danger delete-btn <?= $f['statut'] !== 'brouillon' ? 'is-hidden' : '' ?>" data-id="<?= $f['id'] ?>" title="Supprimer" aria-label="Supprimer">
                            <i class="ri-delete-bin-line" aria-hidden="true" aria-hidden="true"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tr id="no-facture-message" class="is-hidden">
            <td colspan="6" class="table-empty-message">
                Aucune facture enregistrée pour le moment.
            </td>
        </tr>
    </table>
</div>

<!-- MODALE FACTURE -->
<form class="modal" id="modal-facture" method="POST">
    <div class="modal__overlay" data-modal-close></div>
    <div class="modal__container modal__container--lg">
        <?php csrf_field(); ?>
        <input type="hidden" name="id" id="facture-id" value="">

        <div class="modal__header">
            <h3 class="modal__title">Informations Facture</h3>
            <button class="modal__close" data-modal-close type="button">
                <i class="ri-close-line" aria-hidden="true"></i>
            </button>
        </div>

        <div class="modal__body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <select name="client_id" id="facture-client-id" class="form-control" required>
                        <option value="">-- Sélectionner un client --</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= e($client['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro de facture</label>
                    <input id="facture-numero" type="text" class="form-control form-control--readonly" name="numero" value="<?= $nextNumber ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date d'émission</label>
                    <input id="facture-date-emission" type="date" class="form-control" name="date_emission" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date d'échéance</label>
                    <input id="facture-date-echeance" type="date" class="form-control" name="date_echeance" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                </div>
            </div>

            <div class="modal__divider"></div>
            
            <div class="modal__section-header">
                <h4 class="modal__subtitle">Articles / Services</h4>
                <button type="button" id="add-item-row" class="btn--light btn--light--sm">
                    <i class="ri-add-line" aria-hidden="true"></i> Ajouter une ligne
                </button>
            </div>

            <div id="facture-items-container">
                <!-- Les lignes d'articles seront ici -->
            </div>

            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="facture-tva-applicable" name="tva_applicable" checked>
                    Appliquer la TVA (20%)
                </label>
            </div>

            <div class="modal__totals">
                <div class="modal__total-row">
                    <span>Total HT :</span>
                    <span><span id="total-ht">0.00</span> €</span>
                </div>
                <div id="total-tva-row" class="modal__total-row">
                    <span>TVA (20%) :</span>
                    <span><span id="total-tva">0.00</span> €</span>
                </div>
                <div class="modal__total-row modal__total-row--final">
                    <span>Total TTC :</span>
                    <span><span id="total-ttc">0.00</span> €</span>
                </div>
            </div>

            <div class="form-group">
                <label for="facture-notes" class="form-label">Notes</label>
                <textarea id="facture-notes" class="form-control" name="notes" rows="2" placeholder="Informations complémentaires..."></textarea>
            </div>
        </div>

        <div class="modal__footer">
            <button class="btn--light" data-modal-close type="button">Annuler</button>
            <button class="btn--primary" id="modal-save-facture-btn" type="submit">
                <i class="ri-save-line" aria-hidden="true"></i>
                <span>Enregistrer</span>
            </button>
        </div>
    </div>
</form>


