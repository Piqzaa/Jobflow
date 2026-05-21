<h1>Mon Profil</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="alert alert-success">
        Profil mis à jour avec succès !
    </div>
<?php endif; ?>

<form action="<?= url('/profile') ?>" method="POST" enctype="multipart/form-data" class="form-container">
    <?php csrf_field(); ?>

    <div class="form-group">
        <label>Email (non modifiable)</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="form-control">
    </div>

    <div class="row">
        <div class="form-group col">
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group col">
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="entreprise">Nom de l'entreprise</label>
        <input type="text" name="entreprise" id="entreprise" value="<?= htmlspecialchars($user['entreprise'] ?? '') ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="siret">SIRET (14 chiffres)</label>
        <input type="text" name="siret" id="siret" value="<?= htmlspecialchars($user['siret'] ?? '') ?>" class="form-control" maxlength="14">
    </div>

    <div class="form-group">
        <label for="tva_intra">Numéro de TVA Intracommunautaire</label>
        <input type="text" name="tva_intra" id="tva_intra" value="<?= htmlspecialchars($user['tva_intra'] ?? '') ?>" class="form-control" placeholder="FRXXXXXXXXXXXXXXXXX">
    </div>

    <div class="form-group">
        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" class="form-control">
    </div>

    <div class="row">
        <div class="form-group col">
            <label for="iban">IBAN</label>
            <input type="text" name="iban" id="iban" value="<?= htmlspecialchars($user['iban'] ?? '') ?>" class="form-control" placeholder="FR76...">
        </div>
        <div class="form-group col">
            <label for="bic">BIC / SWIFT</label>
            <input type="text" name="bic" id="bic" value="<?= htmlspecialchars($user['bic'] ?? '') ?>" class="form-control" placeholder="XXXXXXXX">
        </div>
    </div>

    <div class="form-group">
        <label for="adresse">Adresse</label>
        <textarea name="adresse" id="adresse" class="form-control"><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>
    </div>

    <div class="row">
        <div class="form-group col">
            <label for="code_postal">Code Postal</label>
            <input type="text" name="code_postal" id="code_postal" value="<?= htmlspecialchars($user['code_postal'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group col">
            <label for="ville">Ville</label>
            <input type="text" name="ville" id="ville" value="<?= htmlspecialchars($user['ville'] ?? '') ?>" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="logo">Logo de l'entreprise</label>
        <?php if (!empty($user['logo_filename'])): ?>
            <div style="margin-bottom: 10px;">
                <img src="<?= url('/uploads/logos/' . $user['logo_filename']) ?>" alt="Logo" style="max-height: 100px; display: block;">
                <input type="hidden" name="current_logo" value="<?= $user['logo_filename'] ?>">
            </div>
        <?php endif; ?>
        <input type="file" name="logo" id="logo" class="form-control">
        <small>Formats acceptés : JPG, PNG, WEBP. Max 2Mo.</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
</form>

<style>
    .form-container { max-width: 800px; margin: 20px 0; }
    .form-group { margin-bottom: 15px; }
    .row { display: flex; gap: 20px; }
    .col { flex: 1; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .btn { padding: 10px 20px; cursor: pointer; border-radius: 4px; border: none; }
    .btn-primary { background-color: #007bff; color: white; }
    .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    ul { margin: 0; padding-left: 20px; }
</style>
