<?php

namespace App\Controllers;

/**
 * Classe de base pour tous les contrôleurs.
 * Permet de centraliser les fonctionnalités communes (authentification, helpers, etc.).
 */
abstract class BaseController {
    
    /**
     * Vérifie si l'utilisateur est connecté.
     * Si non, redirige vers la page de connexion.
     * Pour les requêtes AJAX, renvoie un JSON d'erreur.
     */
    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Si c'est une requête AJAX (on vérifie l'entête ou si on attend du JSON)
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Session expirée']);
                exit;
            }

            // Sinon, redirection classique
            header('Location: ' . url('/login'));
            exit;
        }
    }
}
