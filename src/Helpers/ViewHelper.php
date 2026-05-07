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
