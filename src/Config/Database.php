<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * CLASSE DATABASE
 * 
 * Gère la connexion à la base de données MySQL via PDO.
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'jobflow';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';

        try {
            // DSN (Data Source Name) : définit le type de DB, l'hôte, le nom de la DB et l'encodage
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            
            // Options PDO pour la sécurité et le débug
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lance des exceptions en cas d'erreur SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne les résultats sous forme de tableaux associatifs
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées de MySQL (plus sécure)
            ];

            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion PDO : " . $e->getMessage());
            die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
        }
    }

    /**
     * Singleton : permet de récupérer une instance unique de la connexion
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
