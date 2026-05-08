<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    
    /**
     * Affiche le formulaire de connexion
     */
    public function showLogin() {
        render('auth/login', ['title' => 'Connexion']);
    }

    /**
     * Gère la soumission du formulaire de connexion
     */
    public function login() {
        check_csrf($_POST['csrf_token'] ?? '');

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Succès : on stocke les infos en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            
            // On régénère l'ID de session pour éviter la fixation de session
            session_regenerate_id(true);

            header('Location: /dashboard');
            exit;
        }

        // Échec
        render('auth/login', [
            'title' => 'Connexion',
            'error' => 'Identifiants invalides.'
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
