<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\MongoLogger;

class ProfileController {
    
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

    public function update() {
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $errors = [];

        $data = [
            'nom'         => trim($_POST['nom'] ?? ''),
            'prenom'      => trim($_POST['prenom'] ?? ''),
            'entreprise'  => trim($_POST['entreprise'] ?? ''),
            'siret'       => trim($_POST['siret'] ?? ''),
            'adresse'     => trim($_POST['adresse'] ?? ''),
            'code_postal' => trim($_POST['code_postal'] ?? ''),
            'ville'       => trim($_POST['ville'] ?? ''),
            'telephone'   => trim($_POST['telephone'] ?? ''),
            'tva_intra'   => trim($_POST['tva_intra'] ?? ''),
            'logo_filename' => $_POST['current_logo'] ?? null
        ];

        if (!empty($data['siret']) && !preg_match('/^[0-9]{14}$/', $data['siret'])) {
            $errors[] = "Le numéro SIRET doit contenir exactement 14 chiffres.";
        }

        if (!empty($data['telephone']) && !preg_match('/^[0-9+ .]{10,20}$/', $data['telephone'])) {
            $errors[] = "Le numéro de téléphone n'est pas valide.";
        }
        $newLogo = $this->handleLogoUpload($_FILES['logo'] ?? null, $errors);
        if ($newLogo) {
            $data['logo_filename'] = $newLogo;
        }

        if (!empty($errors)) {
            $userModel = new User();
            $user = $userModel->findById($userId);
            $user = array_merge($user, $data); 
            
            return render('profile', [
                'title' => 'Mon Profil',
                'user' => $user,
                'errors' => $errors
            ]);
        }

        $userModel = new User();
        $userModel->updateProfile($userId, $data);

        MongoLogger::write(
            userId: $userId,
            action: 'update_profile',
            entity: 'user_profile',
            entityId: $userId,
            data: $data
        );

        header('Location: ' . url('/profile?success=1'));
        exit;
    }

    private function handleLogoUpload($file, &$errors) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = "Format de fichier non autorisé (JPG, PNG, WEBP uniquement).";
            return null;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "Le fichier est trop lourd (maximum 2Mo).";
            return null;
        }

        $newFileName = md5(time() . $file['name']) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/logos/';

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        }

        $errors[] = "Erreur lors de l'enregistrement du fichier.";
        return null;
    }
}
