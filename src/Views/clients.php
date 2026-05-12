<h1> Gestions des clients </h1>

<button id="add-client">Ajouter un client</button>

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