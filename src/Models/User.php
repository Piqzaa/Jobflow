<?php

namespace App\Models;

use App\Config\Database;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    public function findById($id) {
        $sql = "SELECT users.email, user_profiles.* 
                FROM users 
                LEFT JOIN user_profiles ON users.id = user_profiles.user_id 
                WHERE users.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function create($email, $password) {
        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        return $stmt->execute([
            $email, 
            password_hash($password, PASSWORD_BCRYPT)
        ]);
    }
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function updateProfile($userId, $data) {
        $sql = "INSERT INTO user_profiles (user_id, nom, prenom, entreprise, siret, adresse) 
                VALUES (?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                nom = VALUES(nom), 
                prenom = VALUES(prenom), 
                entreprise = VALUES(entreprise), 
                siret = VALUES(siret), 
                adresse = VALUES(adresse)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['entreprise'] ?? null,
            $data['siret'] ?? null,
            $data['adresse'] ?? null
        ]);
    }
}
