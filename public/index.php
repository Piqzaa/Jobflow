<?php

/**
 * ARCHITECTURE JOBFLOW - ROUTER PRINCIPAL
 * 
 * Ce fichier est le point d'entrée unique de l'application (Front Controller).
 * Toutes les requêtes HTTP passent par ici grâce à la configuration du serveur (Apache/Nginx).
 */

// 1. Inclusion de l'autoloader de Composer (pour charger nos classes automatiquement)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Chargement des variables d'environnement (si un package comme phpdotenv était installé)
// Pour l'instant, on va créer un petit helper manuel pour lire le .env

/**
 * Charge les variables du fichier .env dans $_ENV
 */
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../.env');

// 3. Initialisation de la session (sécurisée)
ini_set('session.cookie_httponly', 1); // Empêche l'accès au cookie via JS (anti-XSS)
ini_set('session.use_only_cookies', 1); // Force l'utilisation des cookies pour les sessions
session_start();

// 4. Système de routage très basique
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo "<h1>Bienvenue sur Jobflow !</h1>";
echo "<p>Le routeur est prêt. URI actuelle : " . htmlspecialchars($uri) . "</p>";
echo "<p>Dossier racine du projet : " . __DIR__ . "</p>";

// Debug pour vérifier le .env
// echo "<pre>"; print_r($_ENV); echo "</pre>";
