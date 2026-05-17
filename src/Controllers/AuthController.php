<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\MongoLogger;
use App\Services\EmailService;

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

        $token = bin2hex(random_bytes(32));
        $success = $userModel->create($email, $password, $token);

        if ($success) {
            $userId = $userModel->getLastInsertId();
            $userModel->updateProfile($userId, $profileData);

            MongoLogger::write(
                userId: $userId,
                action: 'register_pending_verification',
                entity: 'user',
                entityId: $userId,
                data: ['email' => $email]
            );

            $verifyUrl = url("/verify?token=$token");
            $fullUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $verifyUrl;

            $subject = "Activez votre compte JobFlow";
            $message = "<h1>Bienvenue " . htmlspecialchars($profileData['prenom']) . " !</h1>";
            $message .= "<p>Merci de vous être inscrit sur JobFlow.</p>";
            $message .= "<p>Pour activer votre compte et commencer à gérer vos devis, cliquez sur le bouton ci-dessous :</p>";
            $message .= "<p style='margin: 30px 0;'><a href='$fullUrl' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Activer mon compte</a></p>";
            $message .= "<p>Ou copiez-collez ce lien : <br>$fullUrl</p>";
            $message .= "<p>À bientôt !</p>";

            EmailService::send($email, $subject, $message);

            render('auth/login', [
                'title' => 'Inscription',
                'success' => 'Inscription réussie ! Un email de confirmation a été envoyé à ' . htmlspecialchars($email) . '. Merci de cliquer sur le lien pour activer votre compte.'
            ]);
        } else {
            render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Une erreur est survenue lors de l\'inscription.'
            ]);
        }
    }

    public function verify() {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userModel = new User();
        $success = $userModel->verifyEmail($token);

        if ($success) {
            render('auth/login', [
                'title' => 'Connexion',
                'success' => 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.'
            ]);
        } else {
            render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Le lien de validation est invalide ou a déjà été utilisé.'
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
            if (empty($user['email_verified_at'])) {
                render('auth/login', [
                    'title' => 'Connexion',
                    'error' => 'Votre compte n\'est pas encore activé. Merci de cliquer sur le lien envoyé par email.'
                ]);
                return;
            }

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
