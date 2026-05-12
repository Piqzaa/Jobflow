<h1> Gestions des clients </h1>

<button id="add-client" data-modal-target="#modal-client">Ajouter un client</button>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>SIRET</th>
            <th>Adresse</th>
            <th>Code Postal</th>
            <th>Ville</th>
            <th>Téléphone</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= htmlspecialchars($client['nom'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['siret'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['adresse'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['code_postal'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['ville'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['telephone'] ?? '') ?></td>
            <td><?= htmlspecialchars($client['notes'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<form class="modal" id="modal-client" method="POST" action="<?= url('/clients/add') ?>">
    <div class="modal__overlay"></div>
    
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="modal__content">
        <div class="modal__header">
            <h3 class="modal__title">Ajouter un client</h3>
            <button class="modal__close" data-modal-close type="button">✖</button>
        </div>

        <div class="modal__body">
            <div class="modal__form">
            <label class="modal__label">Nom</label>
            <input
                id="client-nom"
                type="text"
                class="modal__input"
                placeholder="Nom du client"
                name="nom"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">Email</label>
            <input
                id="client-email"
                type="email"
                class="modal__input"
                placeholder="Email du client"
                name="email"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">SIRET</label>
            <input
                id="client-siret"
                type="text"
                class="modal__input"
                placeholder="SIRET du client"
                name="siret"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">Téléphone</label>
            <input
                id="client-tel"
                type="tel"
                pattern="(\+33|0)[0-9 ]{9,14}"
                class="modal__input"
                placeholder="Numéro du client"
                name="telephone"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">Adresse</label>
            <input
                id="client-adresse"
                type="text"
                class="modal__input"
                placeholder="Adresse du client"
                name="adresse"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">Code Postal</label>
            <input
                id="client-code-postal"
                type="text"
                class="modal__input"
                placeholder="Code Postal du client"
                name="code_postal"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">Ville</label>
            <input
                id="client-ville"
                type="text"
                class="modal__input"
                placeholder="Ville du client"
                name="ville"
                required
            />
            </div>

            <div class="modal__form">
            <label class="modal__label">Notes</label>
            <input
                id="client-notes"
                type="text"
                class="modal__input"
                placeholder="Notes sur le client"
                name="notes"
            />
            </div>
        </div>

        <div class="modal__footer">
            <button class="modal__btn-close" data-modal-close type="button">
            Annuler
            </button>
            <button class="modal__btn-add" id="modal-addClient-btn" type="submit">
            ✓ Ajouter
            </button>
        </div>
        </div>
    </form>