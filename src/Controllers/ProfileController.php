<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\MongoLogger;

class ProfileController {
    
    /**
     * Affiche la page de profil
     */
    public function show() {
        $userId = $_SESSION['user_id'];
        $userModel = new User();
        $user = $userModel->findById($userId);

        render('profile', [
            'title' => 'Mon Profil',
            'user' => $user,
            'success' => $_GET['success'] ?? null
        ]);
    }

    /**
     * Traite la modification du profil
     */
    public function update() {
        // 1. Vérification CSRF
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];

        // 2. Collecte des données
        $data = [
            'nom'        => $_POST['nom'] ?? '',
            'prenom'     => $_POST['prenom'] ?? '',
            'entreprise' => $_POST['entreprise'] ?? '',
            'siret'      => $_POST['siret'] ?? '',
            'adresse'    => $_POST['adresse'] ?? '',
        ];

        // 3. Enregistrement SQL
        $userModel = new User();
        $userModel->updateProfile($userId, $data);

        // 4. Log NoSQL (MongoDB)
        MongoLogger::write(
            userId: $userId,
            action: 'update_profile',
            entity: 'user_profile',
            entityId: $userId,
            data: $data
        );

        // 5. Redirection
        header('Location: ' . url('/profile?success=1'));
        exit;
    }
}
