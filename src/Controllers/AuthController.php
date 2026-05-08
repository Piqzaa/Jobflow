<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\MongoLogger;

class AuthController {
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

        // Échec
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
