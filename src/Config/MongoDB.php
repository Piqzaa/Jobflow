<?php

namespace App\Config;

use MongoDB\Client;
use Exception;

class MongoDB {
    private static $instance = null;
    private $database;

    private function __construct() {
        // VÉRIFICATION : L'extension 'mongodb' est-elle installée sur le serveur ?
        if (!extension_loaded('mongodb')) {
            error_log("[MongoDB] L'extension 'mongodb' n'est pas installée sur ce serveur.");
            $this->database = null;
            return;
        }

        $dsn    = $_ENV['MONGO_DSN'] ?? 'mongodb://localhost:27017';
        $dbname = $_ENV['MONGO_DATABASE'] ?? 'jobflow_logs';

        try {
            $client = new \MongoDB\Client($dsn);
            $this->database = $client->$dbname;
        } catch (Exception $e) {
            error_log("Erreur de connexion MongoDB : " . $e->getMessage());
            $this->database = null;
        }
    }
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->database;
    }
}