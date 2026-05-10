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
        // 1. Vérification CSRF pour la sécurité
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $errors = [];

        // 2. Collecte et Nettoyage des données
        $data = [
            'nom'         => trim($_POST['nom'] ?? ''),
            'prenom'      => trim($_POST['prenom'] ?? ''),
            'entreprise'  => trim($_POST['entreprise'] ?? ''),
            'siret'       => trim($_POST['siret'] ?? ''),
            'adresse'     => trim($_POST['adresse'] ?? ''),
            'code_postal' => trim($_POST['code_postal'] ?? ''),
            'ville'       => trim($_POST['ville'] ?? ''),
            'telephone'   => trim($_POST['telephone'] ?? ''),
            'logo_filename' => $_POST['current_logo'] ?? null // On garde l'ancien par défaut
        ];

        // 3. Validation
        // Le SIRET doit faire exactement 14 chiffres
        if (!empty($data['siret']) && !preg_match('/^[0-9]{14}$/', $data['siret'])) {
            $errors[] = "Le numéro SIRET doit contenir exactement 14 chiffres.";
        }

        // Le téléphone (format simple : chiffres, espaces, points, plus)
        if (!empty($data['telephone']) && !preg_match('/^[0-9+ .]{10,20}$/', $data['telephone'])) {
            $errors[] = "Le numéro de téléphone n'est pas valide.";
        }

        // 4. Gestion de l'Upload du Logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['logo']['tmp_name'];
            $fileName = $_FILES['logo']['name'];
            $fileSize = $_FILES['logo']['size'];
            $fileType = $_FILES['logo']['type'];
            
            // On vérifie l'extension
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                // On limite la taille à 2Mo
                if ($fileSize < 2 * 1024 * 1024) {
                    // On génère un nom unique pour éviter les doublons ou l'écrasement
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/uploads/logos/';
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $data['logo_filename'] = $newFileName;
                    } else {
                        $errors[] = "Erreur lors du déplacement du fichier vers le dossier uploads.";
                    }
                } else {
                    $errors[] = "Le fichier est trop lourd (maximum 2Mo).";
                }
            } else {
                $errors[] = "Format de fichier non autorisé (JPG, PNG, WEBP uniquement).";
            }
        }

        // 5. Si erreurs, on renvoie à la vue
        if (!empty($errors)) {
            $userModel = new User();
            $user = $userModel->findById($userId);
            // On fusionne avec les données saisies pour ne pas perdre la saisie de l'utilisateur
            $user = array_merge($user, $data); 
            
            return render('profile', [
                'title' => 'Mon Profil',
                'user' => $user,
                'errors' => $errors
            ]);
        }

        // 6. Enregistrement SQL
        $userModel = new User();
        $userModel->updateProfile($userId, $data);

        // 7. Log NoSQL (MongoDB)
        MongoLogger::write(
            userId: $userId,
            action: 'update_profile',
            entity: 'user_profile',
            entityId: $userId,
            data: $data
        );

        // 8. Redirection
        header('Location: ' . url('/profile?success=1'));
        exit;
    }
}
