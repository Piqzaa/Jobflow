<?php

/**
 * Fonction pour rendre une vue avec un layout
 * 
 * @param string $view Nom du fichier de la vue (sans .php)
 * @param array $data Tableau de données à transmettre à la vue
 */
function render($view, $data = []) {
    if (!isset($data['uri'])) {
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $data['uri'] = str_replace($basePath, '', $requestUri) ?: '/';
    }
    extract($data);
    ob_start();
    
    $viewPath = __DIR__ . '/../Views/' . $view . '.php';
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("La vue '$view' n'existe pas dans $viewPath");
    }
    $content = ob_get_clean();
    require __DIR__ . '/../Views/layout.php';
}

/**
 * Sécurise l'affichage HTML (protection XSS)
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Récupère le contenu d'une vue sous forme de chaîne de caractères
 * (Utile pour les emails)
 */
function view_content($view, $data = []) {
    extract($data);
    ob_start();
    $viewPath = __DIR__ . '/../Views/' . $view . '.php';
    if (file_exists($viewPath)) {
        require $viewPath;
    }
    return ob_get_clean();
}

/**
 * Génère une URL absolue pour le projet
 */
function url($path = '') {
    $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    return $basePath . '/' . ltrim($path, '/');
}
