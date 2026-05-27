<div class="profile">

    <?php if (!empty($errors)): ?>
        <div class="alert alert--danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="profile__alert-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert--success">
            <i class="fas fa-check-circle"></i>
            <span>Profil mis à jour avec succès !</span>
        </div>
    <?php endif; ?>

    <form action="<?= url('/profile') ?>" method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>

        <section class="profile__section">
            <h2 class="profile__section-title">Informations Personnelles</h2>
            
            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="form-control form-control--readonly">
                <small class="form-help">L'email ne peut pas être modifié.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" class="form-control" placeholder="Votre prénom">
                </div>
                <div class="form-group">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" class="form-control" placeholder="Votre nom">
                </div>
            </div>

            <div class="form-group">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" class="form-control" placeholder="06 .. .. .. ..">
            </div>
        </section>

        <section class="profile__section">
            <h2 class="profile__section-title">Informations Entreprise</h2>
            
            <div class="form-group">
                <label for="entreprise" class="form-label">Nom de l'entreprise</label>
                <input type="text" name="entreprise" id="entreprise" value="<?= htmlspecialchars($user['entreprise'] ?? '') ?>" class="form-control" placeholder="Nom de votre structure">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="siret" class="form-label">SIRET</label>
                    <input type="text" name="siret" id="siret" value="<?= htmlspecialchars($user['siret'] ?? '') ?>" class="form-control" maxlength="14" placeholder="14 chiffres">
                </div>
                <div class="form-group">
                    <label for="tva_intra" class="form-label">TVA Intracommunautaire</label>
                    <input type="text" name="tva_intra" id="tva_intra" value="<?= htmlspecialchars($user['tva_intra'] ?? '') ?>" class="form-control" placeholder="FRXXXXXXXXXXXXXXXXX">
                </div>
            </div>

            <div class="form-group">
                <label for="adresse" class="form-label">Adresse professionnelle</label>
                <textarea name="adresse" id="adresse" class="form-control" rows="3" placeholder="Numéro, rue..."><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="code_postal" class="form-label">Code Postal</label>
                    <input type="text" name="code_postal" id="code_postal" value="<?= htmlspecialchars($user['code_postal'] ?? '') ?>" class="form-control" placeholder="75000">
                </div>
                <div class="form-group">
                    <label for="ville" class="form-label">Ville</label>
                    <input type="text" name="ville" id="ville" value="<?= htmlspecialchars($user['ville'] ?? '') ?>" class="form-control" placeholder="Paris">
                </div>
            </div>
        </section>

        <section class="profile__section">
            <h2 class="profile__section-title">Paramètres Avancés</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="iban" class="form-label">IBAN</label>
                    <input type="text" name="iban" id="iban" value="<?= htmlspecialchars($user['iban'] ?? '') ?>" class="form-control" placeholder="FR76...">
                </div>
                <div class="form-group">
                    <label for="bic" class="form-label">BIC / SWIFT</label>
                    <input type="text" name="bic" id="bic" value="<?= htmlspecialchars($user['bic'] ?? '') ?>" class="form-control" placeholder="XXXXXXXX">
                </div>
            </div>
            
            <div class="profile__logo-container">
                <label class="form-label">Logo de l'entreprise</label>
                <div class="profile__logo-content">
                    <?php if (!empty($user['logo_filename'])): ?>
                        <div class="profile__logo-preview">
                            <img src="<?= url('/uploads/logos/' . $user['logo_filename']) ?>" alt="Logo actuel" class="profile__logo-img">
                            <input type="hidden" name="current_logo" value="<?= $user['logo_filename'] ?>">
                        </div>
                    <?php endif; ?>
                    <div class="profile__logo-upload">
                        <input type="file" name="logo" id="logo" class="form-control">
                        <p class="profile__help-text">JPG, PNG, WEBP. Max 2Mo.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="profile__footer">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
