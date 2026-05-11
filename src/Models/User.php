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
    public function create($email, $password, $token = null) {
        $stmt = $this->db->prepare("INSERT INTO users (email, password, verification_token) VALUES (?, ?, ?)");
        return $stmt->execute([
            $email, 
            password_hash($password, PASSWORD_BCRYPT),
            $token
        ]);
    }

    public function verifyEmail($token) {
        // 1. On cherche l'utilisateur qui a ce token
        $stmt = $this->db->prepare("SELECT id FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            // 2. On met à jour : on valide l'email et on supprime le token
            $stmt = $this->db->prepare("UPDATE users SET email_verified_at = NOW(), verification_token = NULL WHERE id = ?");
            return $stmt->execute([$user['id']]);
        }

        return false;
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function updateProfile($userId, $data) {
        $sql = "INSERT INTO user_profiles (user_id, nom, prenom, entreprise, siret, adresse, code_postal, ville, telephone, logo_filename) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                nom = VALUES(nom), 
                prenom = VALUES(prenom), 
                entreprise = VALUES(entreprise), 
                siret = VALUES(siret), 
                adresse = VALUES(adresse),
                code_postal = VALUES(code_postal),
                ville = VALUES(ville),
                telephone = VALUES(telephone),
                logo_filename = VALUES(logo_filename)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['entreprise'] ?? null,
            $data['siret'] ?? null,
            $data['adresse'] ?? null,
            $data['code_postal'] ?? null,
            $data['ville'] ?? null,
            $data['telephone'] ?? null,
            $data['logo_filename'] ?? null
        ]);
    }
}
