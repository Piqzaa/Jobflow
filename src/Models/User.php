<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Trouve un utilisateur par son email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create($email, $password) {
        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        // On ne stocke JAMAIS le mot de passe en clair, mais le hash
        return $stmt->execute([
            $email, 
            password_hash($password, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Récupère l'ID du dernier utilisateur inséré
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}
