<h1> Mes Devis </h1>

<button id="add-devis" data-modal-target="#modal-devis">Ajouter un devis</button>

<table id="devis-table" 
    data-delete-url="<?= url('/devis/delete') ?>"
    data-get-url="<?= url('/devis/get') ?>"
    data-update-url="<?= url('/devis/update') ?>"
    data-status-url="<?= url('/devis/status') ?>"
>
    <thead>
        <tr>
            <th>Numéro</th>
            <th>Client</th>
            <th>Date Émission</th>
            <th>Date Validité</th>
            <th>Montant TTC</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($devis as $d): ?>
        <tr data-id="<?= $d['id'] ?>">
            <td class="d-numero"><?= htmlspecialchars($d['numero'] ?? '') ?></td>
            <td class="d-client"><?= htmlspecialchars($d['client_nom'] ?? '') ?></td>
            <td class="d-date-emission"><?= htmlspecialchars($d['date_emission'] ?? '') ?></td>
            <td class="d-date-validite"><?= htmlspecialchars($d['date_validite'] ?? '') ?></td>
            <td class="d-montant-ttc"><?= number_format($d['montant_ttc'] ?? 0, 2, ',', ' ') ?> €</td>
            <td class="d-statut">
                <select class="status-select badge-select badge--<?= $d['statut'] ?>" data-id="<?= $d['id'] ?>">
                    <option value="brouillon" <?= $d['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="envoye" <?= $d['statut'] === 'envoye' ? 'selected' : '' ?>>Envoyé</option>
                    <option value="accepte" <?= $d['statut'] === 'accepte' ? 'selected' : '' ?>>Accepté</option>
                    <option value="refuse" <?= $d['statut'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                    <option value="expire" <?= $d['statut'] === 'expire' ? 'selected' : '' ?>>Expiré</option>
                </select>
            </td>
            <td>
                <button class="view-pdf-btn" data-id="<?= $d['id'] ?>" title="Voir PDF">👁️</button>
                <button class="edit-btn" data-id="<?= $d['id'] ?>" title="Modifier">✏️</button>
                <button class="delete-btn" data-id="<?= $d['id'] ?>" title="Supprimer">🗑️</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- MODALE DEVIS -->
<form class="modal" id="modal-devis" method="POST" action="<?= url('/devis/add') ?>">
    <div class="modal__overlay"></div>
    
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" id="devis-id" value="">

    <div class="modal__content">
        <div class="modal__header">
            <h3 class="modal__title">Ajouter un devis</h3>
            <button class="modal__close" data-modal-close type="button">✖</button>
        </div>

        <div class="modal__body">
            <!-- Choix du Client -->
            <div class="modal__form">
                <label class="modal__label">Client</label>
                <select name="client_id" id="devis-client-id" class="modal__input" required>
                    <option value="">-- Sélectionner un client --</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Numéro du devis -->
            <div class="modal__form">
                <label class="modal__label">Numéro de devis</label>
                <input
                    id="devis-numero"
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
                    id="devis-date-emission"
                    type="date"
                    class="modal__input"
                    name="date_emission"
                    value="<?= date('Y-m-d') ?>"
                    required
                />
            </div>

            <div class="modal__form">
                <label class="modal__label">Date de validité (Défaut 30 jours)</label>
                <input
                    id="devis-date-validite"
                    type="date"
                    class="modal__input"
                    name="date_validite"
                    value="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                />
            </div>

            <hr>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h4 style="margin: 0;">Articles / Services</h4>
                <button type="button" id="add-item-row" class="modal__btn-add" style="background: #28a745; padding: 5px 10px; font-size: 0.8em;">+ Ajouter une ligne</button>
            </div>
            <div id="devis-items-container">
                <div class="devis-item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="item_designation[]" placeholder="Désignation" class="modal__input" style="flex: 3;" required>
                    <input type="number" name="item_quantite[]" placeholder="Qté" class="modal__input item-qty" style="flex: 1;" value="1" step="0.01" required>
                    <input type="number" name="item_prix[]" placeholder="Prix Unit. HT" class="modal__input item-price" style="flex: 1;" step="0.01" required>
                    <button type="button" class="remove-item-row" style="background: none; border: none; cursor: pointer; color: #dc3545;">✖</button>
                </div>
            </div>

            <div class="modal__form" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
                <input type="checkbox" id="devis-tva-applicable" name="tva_applicable" checked style="width: 20px; height: 20px;">
                <label for="devis-tva-applicable">Appliquer la TVA (20%)</label>
            </div>

            <div class="modal__total" style="background: #f9f9f9; padding: 10px; margin-top: 10px; border-radius: 5px; text-align: right;">
                <div>Total HT : <span id="total-ht">0.00</span> €</div>
                <div id="tva-row">TVA (20%) : <span id="total-tva">0.00</span> €</div>
                <div style="font-weight: bold; font-size: 1.2em;">Total TTC : <span id="total-ttc">0.00</span> €</div>
            </div>

            <div class="modal__form">
                <label class="modal__label">Notes (Interne ou pour le client)</label>
                <textarea
                    id="devis-notes"
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
            <button class="modal__btn-add" id="modal-save-devis-btn" type="submit">
                ✓ Enregistrer
            </button>
        </div>
    </div>
</form>
