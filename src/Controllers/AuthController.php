<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\MongoLogger;

class AuthController {
    public function showRegister() {
        render('auth/register', ['title' => 'Inscription']);
    }
    public function register() {
        check_csrf($_POST['csrf_token'] ?? '');
        $profileData = [
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'entreprise' => $_POST['entreprise'] ?? '',
            'siret' => $_POST['siret'] ?? '',
            'adresse' => $_POST['adresse'] ?? '',
            'code_postal' => $_POST['code_postal'] ?? '',
            'ville' => $_POST['ville'] ?? '',
            'telephone' => $_POST['telephone'] ?? ''
        ];
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';
        $userModel = new User();

        if ($password !== $password_confirmation) {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Les mots de passe ne correspondent pas.'
            ]);
            return;
        }

        if ($userModel->findByEmail($email)) {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Email déjà utilisé.'
            ]);
            return;
        }

        if (strlen($password) < 8) {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Le mot de passe doit contenir au moins 8 caractères.'
            ]);
            return;
        }

        if(!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.'
            ]);
            return;
        }
        
        if (!$profileData['nom'] || !$profileData['prenom']) {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Le nom et le prénom sont requis.'
            ]);
            return;
        
        }

        $success = $userModel->create($email, $password);
        if ($success) {
            $userId = $userModel->getLastInsertId();
            MongoLogger::write(
                userId: $userId,
                action: 'register',
                entity: 'user',
                entityId: $userId,
                data: ['email' => $email]
            );
            $userModel->updateProfile($userId, $profileData);

            render('auth/register', [
                'title' => 'Inscription',
                'success' => 'Inscription effectuée avec succès ! Vous pouvez maintenant vous connecter.'
            ]);
        } else {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Une erreur est survenue lors de l\'inscription.'
            ]);
        }
    }

    public function showLogin() {
        render('auth/login', ['title' => 'Connexion']);
    }

    public function login() {
        check_csrf($_POST['csrf_token'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            session_regenerate_id(true);

            // Log de la connexion
            MongoLogger::write(
                userId: $user['id'],
                action: 'login',
                entity: 'user',
                entityId: $user['id'],
                data: [
                    'email' => $user['email'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]
            );

            header('Location: ' . url('/dashboard'));
            exit;
        }

        // Échec de connexion
        MongoLogger::write(
            userId: null,
            action: 'failed_login',
            entity: 'user',
            entityId: null,
            data: [
                'email' => $email,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]
        );

        render('auth/login', [
            'title' => 'Connexion',
            'error' => 'Identifiants invalides.'
        ]);
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ' . url('/'));
        exit;
    }
}
