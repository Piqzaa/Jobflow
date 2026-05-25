<header class="page-header">
    <div class="page-actions">
        <button class="btn-primary" id="add-devis" data-modal-target="#modal-devis">
            <i class="ri-add-line"></i>
            <span>Nouveau devis</span>
        </button>
    </div>
</header>

<div class="table-container">
    <table class="data-table" id="devis-table" 
        data-delete-url="<?= url('/devis/delete') ?>"
        data-get-url="<?= url('/devis/get') ?>"
        data-update-url="<?= url('/devis/update') ?>"
        data-status-url="<?= url('/devis/status') ?>"
    >
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Validité</th>
                <th>Montant TTC</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($devis as $d): ?>
            <tr data-id="<?= $d['id'] ?>">
                <td class="d-numero" data-label="Numéro">
                    <span class="text-main"><?= htmlspecialchars($d['numero'] ?? '') ?></span>
                </td>
                <td class="d-client" data-label="Client">
                    <span class="text-main"><?= htmlspecialchars($d['client_nom'] ?? '') ?></span>
                </td>
                <td class="d-date-validite" data-label="Validité">
                    <span class="text-sub"><?= htmlspecialchars($d['date_validite'] ?? '') ?></span>
                </td>
                <td class="d-montant-ttc" data-label="Montant TTC">
                    <span class="text-main"><?= number_format($d['montant_ttc'] ?? 0, 2, ',', ' ') ?> €</span>
                </td>
                <td class="d-statut" data-label="Statut">
                    <select class="status-select badge-select badge--<?= $d['statut'] ?>" data-id="<?= $d['id'] ?>">
                        <option value="brouillon" <?= $d['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="envoye" <?= $d['statut'] === 'envoye' ? 'selected' : '' ?>>Envoyé</option>
                        <option value="accepte" <?= $d['statut'] === 'accepte' ? 'selected' : '' ?>>Accepté</option>
                        <option value="refuse" <?= $d['statut'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                        <option value="expire" <?= $d['statut'] === 'expire' ? 'selected' : '' ?>>Expiré</option>
                    </select>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn-action view-pdf-btn" data-id="<?= $d['id'] ?>" title="Voir PDF">
                            <i class="ri-file-pdf-line"></i>
                        </button>
                        <?php if ($d['statut'] === 'accepte'): ?>
                            <button class="btn-action convert-btn" data-id="<?= $d['id'] ?>" title="Convertir en Facture">
                                <i class="ri-exchange-funds-line"></i>
                            </button>
                        <?php endif; ?>
                        <button class="btn-action edit-btn" data-id="<?= $d['id'] ?>" title="Modifier">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button class="btn-action btn-action--danger delete-btn" data-id="<?= $d['id'] ?>" title="Supprimer">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- MODALE DEVIS -->
<form class="modal" id="modal-devis" method="POST" action="<?= url('/devis/add') ?>">
    <div class="modal__overlay" data-modal-close></div>
    <div class="modal__container modal__container--lg">
        <?php csrf_field(); ?>
        <input type="hidden" name="id" id="devis-id" value="">

        <div class="modal__header">
            <h3 class="modal__title">Informations Devis</h3>
            <button class="modal__close" data-modal-close type="button">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <div class="modal__body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <select name="client_id" id="devis-client-id" class="form-control" required>
                        <option value="">-- Sélectionner un client --</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro de devis</label>
                    <input id="devis-numero" type="text" class="form-control form-control--readonly" name="numero" value="<?= $nextNumber ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date d'émission</label>
                    <input id="devis-date-emission" type="date" class="form-control" name="date_emission" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date de validité</label>
                    <input id="devis-date-validite" type="date" class="form-control" name="date_validite" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>

            <div class="modal__divider"></div>
            
            <div class="modal__section-header">
                <h4 class="modal__subtitle">Articles / Services</h4>
                <button type="button" id="add-item-row" class="btn-light btn-light--sm">
                    <i class="ri-add-line"></i> Ajouter une ligne
                </button>
            </div>

            <div id="devis-items-container">
                <!-- Les lignes d'articles seront ici -->
            </div>

            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="devis-tva-applicable" name="tva_applicable" checked>
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
                <label for="devis-notes" class="form-label">Notes</label>
                <textarea id="devis-notes" class="form-control" name="notes" rows="2" placeholder="Informations complémentaires..."></textarea>
            </div>
        </div>

        <div class="modal__footer">
            <button class="btn-light" data-modal-close type="button">Annuler</button>
            <button class="btn-primary" id="modal-save-devis-btn" type="submit">
                <i class="ri-save-line"></i>
                <span>Enregistrer</span>
            </button>
        </div>
    </div>
</form>
