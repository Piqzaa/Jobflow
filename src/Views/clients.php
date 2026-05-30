<header class="page-header">
    <div class="page-actions">
        <button class="btn--primary" id="add-client" data-modal-target="#modal-client">
            <i class="ri-user-add-line" aria-hidden="true"></i>
            <span>Nouveau client</span>
        </button>
    </div>
</header>

<div class="table-container">
    <table class="data-table" id="clients-table" 
        data-delete-url="<?= url('/clients/delete') ?>"
        data-get-url="<?= url('/clients/get') ?>"
        data-update-url="<?= url('/clients/update') ?>"
    >
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Ville</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
            <tr data-id="<?= $client['id'] ?>">
                <td class="c-nom" data-label="Nom"><?= e($client['nom']) ?></td>
                <td class="c-email" data-label="Email"><?= e($client['email']) ?></td>
                <td class="c-ville" data-label="Ville"><?= e($client['ville']) ?></td>
                <td class="c-telephone" data-label="Tel"><?= e($client['telephone']) ?></td>
                <td data-label="Actions">
                    <div class="table-actions">
                        <button class="btn-action edit-client-btn" data-id="<?= $client['id'] ?>" title="Modifier" aria-label="Modifier">
                            <i class="ri-pencil-line" aria-hidden="true" aria-hidden="true"></i>
                        </button>
                        <button class="btn-action btn-action--danger delete-client-btn" data-id="<?= $client['id'] ?>" title="Supprimer" aria-label="Supprimer">
                            <i class="ri-delete-bin-line" aria-hidden="true" aria-hidden="true"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Client -->
<form class="modal" id="modal-client" method="POST" action="<?= url('/clients/add') ?>">
    <div class="modal__overlay" data-modal-close></div>
    <div class="modal__container">
        <?php csrf_field(); ?>
        <input type="hidden" name="id" id="client-id" value="">

        <div class="modal__header">
            <h3 class="modal__title">Informations Client</h3>
            <button class="modal__close" data-modal-close type="button">
                <i class="ri-close-line" aria-hidden="true"></i>
            </button>
        </div>

        <div class="modal__body">
            <div class="form-row">
                <div class="form-group">
                    <label for="client-nom" class="form-label">Nom / Entreprise</label>
                    <input type="text" name="nom" id="client-nom" placeholder="Dupont" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="client-email" class="form-label">Email</label>
                    <input type="email" name="email" id="client-email" placeholder="dupont@example.com" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="client-siret" class="form-label">SIRET</label>
                    <input type="text" name="siret" id="client-siret" placeholder="123 456 789 00012" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="client-tel" class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" id="client-tel" class="form-control" placeholder="06 00 00 00 00" required>
                </div>
            </div>

            <div class="form-group">
                <label for="client-adresse" class="form-label">Adresse</label>
                <input type="text" name="adresse" id="client-adresse" placeholder="123 Rue de Paris" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="client-code-postal" class="form-label">Code Postal</label>
                    <input type="text" name="code_postal" id="client-code-postal" placeholder="75000" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="client-ville" class="form-label">Ville</label>
                    <input type="text" name="ville" id="client-ville" placeholder="Paris" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="client-notes" class="form-label">Notes</label>
                <textarea name="notes" id="client-notes" class="form-control" placeholder="Informations complémentaires..." rows="2" ></textarea>
            </div>
        </div>

        <div class="modal__footer">
            <button class="btn--light" data-modal-close type="button">Annuler</button>
            <button class="btn--primary" id="modal-addClient-btn" type="submit">✓ Enregistrer</button>
        </div>
    </div>
</form>


