<?php

/**
 * ARCHITECTURE JOBFLOW - ROUTER PRINCIPAL
 */

// 1. Inclusion de l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Helpers/SecurityHelper.php';

// 2. Sécurité : Headers HTTP
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

// 3. Inclusion manuelle de nos Helpers
require_once __DIR__ . '/../src/Helpers/ViewHelper.php';

// 3. Chargement des variables d'environnement
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (empty(trim($line)) || strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
        }
    }
}
loadEnv(__DIR__ . '/../.env');

// 4. Initialisation de la session
session_start();

// 5. Système de routage (Amélioré pour gérer les sous-dossiers)
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace($basePath, '', $requestUri);

if (empty($uri)) $uri = '/';
$method = $_SERVER['REQUEST_METHOD'];

// On simule un petit routeur
switch ($uri) {
    case '/':
        render('home', ['title' => 'Accueil']);
        break;

    case '/login':
        $auth = new \App\Controllers\AuthController();
        if ($method === 'GET') {
            $auth->showLogin();
        } else {
            $auth->login();
        }
        break;

    case '/logout':
        $auth = new \App\Controllers\AuthController();
        $auth->logout();
        break;

    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        render('dashboard', ['title' => 'Tableau de bord']);
        break;

    default:
        http_response_code(404);
        render('home', ['title' => '404 Non Trouvé', 'content' => '<h1>404 - Page non trouvée</h1>']);
        break;
}
