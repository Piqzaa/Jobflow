<h1>Mon Profil</h1>

<form action="<?= url('/profile') ?>" method="POST" class="form-container">
    <?php csrf_field(); ?>

    <div class="form-group">
        <label>Email (non modifiable)</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="form-control">
    </div>

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="entreprise">Nom de l'entreprise</label>
        <input type="text" name="entreprise" id="entreprise" value="<?= htmlspecialchars($user['entreprise'] ?? '') ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="siret">SIRET</label>
        <input type="text" name="siret" id="siret" value="<?= htmlspecialchars($user['siret'] ?? '') ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="adresse">Adresse</label>
        <textarea name="adresse" id="adresse" class="form-control"><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
</form>

<style>
    .form-container { max-width: 600px; margin: 20px 0; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .btn { padding: 10px 20px; cursor: pointer; border-radius: 4px; border: none; }
    .btn-primary { background-color: #007bff; color: white; }
</style>
