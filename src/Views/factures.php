<h1> Mes Factures </h1>

<button id="add-facture" data-modal-target="#modal-facture">Ajouter une facture</button>

<table id="factures-table"
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
            <th>Émise le</th>
            <th>Échéance</th>
            <th>Montant TTC</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($factures as $f): ?>
        <tr data-id="<?= $f['id'] ?>">
            <td class="f-numero"><?= htmlspecialchars($f['numero']) ?></td>
            <td class="f-client"><?= htmlspecialchars($f['client_nom']) ?></td>
            <td class="f-date-emission"><?= date('d/m/Y', strtotime($f['date_emission'])) ?></td>
            <td class="f-date-echeance"><?= date('d/m/Y', strtotime($f['date_echeance'])) ?></td>
            <td class="f-montant-ttc"><?= number_format($f['montant_ttc'], 2, ',', ' ') ?> €</td>
            <td class="f-statut">
                <select class="status-select badge-select badge--<?= $f['statut'] ?>" data-id="<?= $f['id'] ?>">
                    <option value="brouillon" <?= $f['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="envoyee" <?= $f['statut'] === 'envoyee' ? 'selected' : '' ?>>Envoyée</option>
                    <option value="payee" <?= $f['statut'] === 'payee' ? 'selected' : '' ?>>Payée</option>
                    <option value="annulee" <?= $f['statut'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                </select>
            </td>
            <td>
                <a href="<?= url('/facture/pdf?id=' . $f['id']) ?>" target="_blank" class="action-btn" title="Voir PDF">👁️</a>
                <button class="edit-btn <?= $f['statut'] !== 'brouillon' ? 'is-hidden' : '' ?>" data-id="<?= $f['id'] ?>" title="Modifier">✏️</button>
                <button class="delete-btn <?= $f['statut'] !== 'brouillon' ? 'is-hidden' : '' ?>" data-id="<?= $f['id'] ?>" title="Supprimer">🗑️</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($factures)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">Aucune facture pour le moment.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<form class="modal" id="modal-facture" method="POST">
    <div class="modal__overlay"></div>
    
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" id="facture-id" value="">

    <div class="modal__content">
        <div class="modal__header">
            <h3 class="modal__title">Ajouter une facture</h3>
            <button class="modal__close" data-modal-close type="button">✖</button>
        </div>

        <div class="modal__body">
            <div class="modal__form">
                <label class="modal__label">Client</label>
                <select name="client_id" id="facture-client-id" class="modal__input" required>
                    <option value="">-- Sélectionner un client --</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal__form">
                <label class="modal__label">Numéro de facture</label>
                <input
                    id="facture-numero"
                    type="text"
                    class="modal__input"
                    name="numero"
                    value="<?= $nextNumber ?>" 
                    readonly
                    style="background-color: #f0f0f0; cursor: not-allowed;"
                />
            </div>

            <div class="modal__form">
                <label class="modal__label">Date d'émission</label>
                <input
                    id="facture-date-emission"
                    type="date"
                    class="modal__input"
                    name="date_emission"
                    value="<?= date('Y-m-d') ?>"
                    required
                />
            </div>

            <div class="modal__form">
                <label class="modal__label">Date d'échéance</label>
                <input
                    id="facture-date-echeance"
                    type="date"
                    class="modal__input"
                    name="date_echeance"
                    value="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                    required
                />
            </div>

            <hr>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h4 style="margin: 0;">Articles / Services</h4>
                <button type="button" id="add-item-row" class="modal__btn-add" style="background: #28a745; padding: 5px 10px; font-size: 0.8em;">+ Ajouter une ligne</button>
            </div>
            <div id="facture-items-container">
                <div class="facture-item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="item_designation[]" placeholder="Désignation" class="modal__input" style="flex: 3;" required>
                    <input type="number" name="item_quantite[]" placeholder="Qté" class="modal__input item-qty" style="flex: 1;" value="1" step="0.01" required>
                    <input type="number" name="item_prix[]" placeholder="Prix Unit. HT" class="modal__input item-price" style="flex: 1;" step="0.01" required>
                    <button type="button" class="remove-item-row" style="background: none; border: none; cursor: pointer; color: #dc3545;">✖</button>
                </div>
            </div>

            <div class="modal__form" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
                <input type="checkbox" id="facture-tva-applicable" name="tva_applicable" checked style="width: 20px; height: 20px;">
                <label for="facture-tva-applicable">Appliquer la TVA (20%)</label>
            </div>

            <div class="modal__total" style="background: #f9f9f9; padding: 10px; margin-top: 10px; border-radius: 5px; text-align: right;">
                <div>Total HT : <span id="total-ht">0.00</span> €</div>
                <div id="tva-row">TVA (20%) : <span id="total-tva">0.00</span> €</div>
                <div style="font-weight: bold; font-size: 1.2em;">Total TTC : <span id="total-ttc">0.00</span> €</div>
            </div>

            <div class="modal__form">
                <label class="modal__label">Notes</label>
                <textarea
                    id="facture-notes"
                    class="modal__input"
                    name="notes"
                    rows="3"
                ></textarea>
            </div>
        </div>

        <div class="modal__footer">
            <button class="modal__btn-close" data-modal-close type="button">
                Annuler
            </button>
            <button class="modal__btn-add" id="modal-save-facture-btn" type="submit">
                ✓ Enregistrer
            </button>
        </div>
    </div>
</form>
