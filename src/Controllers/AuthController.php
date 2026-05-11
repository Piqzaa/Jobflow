<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function showRegister() {
        render('auth/register', ['title' => 'Inscription']);
    }

    public function register() {
        check_csrf($_POST['csrf_token'] ?? '');
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
        
        $userModel->create($email, $password);
        render('auth/register', [
            'title' => 'Inscription',
            'success' => 'Inscription effectuée'
        ]);
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
            header('Location: ' . url('/dashboard'));
            exit;
        }
        // Échec
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
