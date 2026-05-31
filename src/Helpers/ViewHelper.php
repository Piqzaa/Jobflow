<?php

/**
 * Fonction pour rendre une vue avec un layout
 * 
 * @param string $view Nom du fichier de la vue (sans .php)
 * @param array $data Tableau de données à transmettre à la vue
 */
function render($view, $data = []) {
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
    
    // Sur certains hébergements comme InfinityFree, on redirige la racine vers /public.
    // On nettoie donc le basePath pour éviter d'avoir '/public/' dans toutes nos URLs.
    $basePath = str_replace('/public', '', $basePath);
    
    return $basePath . '/' . ltrim($path, '/');
}
