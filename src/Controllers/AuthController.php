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

            header('Location: ' . url('/dashboard'));
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
        // 1. On vide toutes les variables de session
        $_SESSION = [];

        // 2. On supprime le cookie de session dans le navigateur
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. On détruit la session sur le serveur
        session_destroy();

        header('Location: ' . url('/'));
        exit;
    }
}
