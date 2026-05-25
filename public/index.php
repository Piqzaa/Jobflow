<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Helpers/SecurityHelper.php';
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data:;");
require_once __DIR__ . '/../src/Helpers/ViewHelper.php';

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
session_start();

$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace($basePath, '', $requestUri);

if (empty($uri)) $uri = '/';
$method = $_SERVER['REQUEST_METHOD'];


switch ($uri) {
    case '/':
        render('home', ['title' => 'Accueil']);
        break;

    case '/register':
        $auth = new \App\Controllers\AuthController();
        if ($method === 'GET') {
            $auth->showRegister();
        } else {
            $auth->register();
        }
        break;

    case '/forgot-password':
        $auth = new \App\Controllers\AuthController();
        if ($method === 'GET') {
            $auth->showForgotPassword();
        } else {
            $auth->forgotPassword();
        }
        break;

    case '/reset-password':
        $auth = new \App\Controllers\AuthController();
        if ($method === 'GET') {
            $auth->showResetPassword();
        } else {
            $auth->resetPassword();
        }
        break;

    case '/verify':
        $auth = new \App\Controllers\AuthController();
        $auth->verify();
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
        $dashboard = new \App\Controllers\DashboardController();
        $dashboard->index();
        break;

    case '/profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $profile = new \App\Controllers\ProfileController();
        if ($method === 'GET') {
            $profile->show();
        } else {
            $profile->update();
        }
        break;

    case '/devis':
        $devisController = new \App\Controllers\DevisController();
        $devisController->index();
        break;

    case '/devis/get':
        $devisController = new \App\Controllers\DevisController();
        $devisController->get();
        break;
        
    case '/devis/add':
        $devisController = new \App\Controllers\DevisController();
        $devisController->create();
        break;

    case '/devis/delete':
        $devisController = new \App\Controllers\DevisController();
        $devisController->delete();
        break;

    case '/devis/update':
        $devisController = new \App\Controllers\DevisController();
        $devisController->update();
        break;

    case '/devis/status':
        $devisController = new \App\Controllers\DevisController();
        $devisController->updateStatus();
        break;

    case '/devis/pdf':
        $devisController = new \App\Controllers\DevisController();
        $devisController->pdf();
        break;

    case '/factures':
        $factureController = new \App\Controllers\FactureController();
        $factureController->index();
        break;

    case '/facture/get':
        $factureController = new \App\Controllers\FactureController();
        $factureController->get();
        break;

    case '/facture/add':
        $factureController = new \App\Controllers\FactureController();
        $factureController->create();
        break;

    case '/facture/update':
        $factureController = new \App\Controllers\FactureController();
        $factureController->update();
        break;

    case '/facture/delete':
        $factureController = new \App\Controllers\FactureController();
        $factureController->delete();
        break;

    case '/facture/status':
        $factureController = new \App\Controllers\FactureController();
        $factureController->updateStatus();
        break;

    case '/facture/convert':
        $factureController = new \App\Controllers\FactureController();
        $factureController->createFromDevis();
        break;

    case '/facture/pdf':
        $factureController = new \App\Controllers\FactureController();
        $factureController->pdf();
        break;
        
    case '/clients':
        $clientController = new \App\Controllers\ClientController();
        $clientController->index();
        break;
    
    case '/clients/add':
        $clientController = new \App\Controllers\ClientController();
        $clientController->create();
        break;

    case '/clients/update':
        $clientController = new \App\Controllers\ClientController();
        $clientController->update();
        break;
    
    case '/clients/delete':
        $clientController = new \App\Controllers\ClientController();
        $clientController->delete();
        break;
        
    case '/clients/get':
        $clientController = new \App\Controllers\ClientController();
        $clientController->get();
        break;

    case '/tva':
        $tvaController = new \App\Controllers\TvaController();
        $tvaController->index();
        break;

    case '/tva/add':
        $tvaController = new \App\Controllers\TvaController();
        $tvaController->create();
        break;

    case '/tva/delete':
        $tvaController = new \App\Controllers\TvaController();
        $tvaController->delete();
        break;
        
    default:
        http_response_code(404);
        render('home', ['title' => '404 Non Trouvé', 'content' => '<h1>404 - Page non trouvée</h1>']);
        break;
}
