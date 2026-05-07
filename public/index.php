<?php

/**
 * ARCHITECTURE JOBFLOW - ROUTER PRINCIPAL
 */

// 1. Inclusion de l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

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

// 5. Système de routage
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// On simule un petit routeur
switch ($uri) {
    case '/':
    case '/index.php':
        render('home', ['title' => 'Accueil']);
        break;

    case '/login':
        echo "Page de connexion (à venir)";
        break;

    default:
        http_response_code(404);
        render('home', ['title' => '404 Non Trouvé', 'content' => '<h1>404 - Page non trouvée</h1>']);
        break;
}
