<?php

namespace App\Config;

use MongoDB\Client;
use Exception;

class MongoDB {
    private static $instance = null;
    private $database;

    private function __construct() {
        $host   = $_ENV['MONGO_HOST'] ?? 'localhost';
        $port   = $_ENV['MONGO_PORT'] ?? '27017';
        $dbname = $_ENV['MONGO_DB']   ?? 'jobflow_logs';

        try {
            $client = new Client("mongodb://$host:$port");
            $this->database = $client->$dbname;
        } catch (Exception $e) {
            die("Erreur de connexion MongoDB : " . $e->getMessage());
        }
    }
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->database;
    }
}