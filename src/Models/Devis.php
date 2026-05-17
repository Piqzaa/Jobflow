<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Devis {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Liste tous les devis d'un utilisateur avec le nom du client
     */
    public function getAllByUser($userId) {
        $sql = "SELECT d.*, c.nom as client_nom 
                FROM devis d 
                JOIN clients c ON d.client_id = c.id 
                WHERE d.user_id = :user_id 
                AND d.deleted_at IS NULL 
                ORDER BY d.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère un devis spécifique
     */
    public function getById($id, $userId) {
        $sql = "SELECT d.*, c.nom as client_nom 
                FROM devis d 
                JOIN clients c ON d.client_id = c.id 
                WHERE d.id = :id AND d.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Crée l'en-tête d'un devis
     */
    public function create($userId, $data) {
        $sql = "INSERT INTO devis (user_id, client_id, numero, date_emission, date_validite, notes) 
                VALUES (:user_id, :client_id, :numero, :date_emission, :date_validite, :notes)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'       => $userId,
            'client_id'     => $data['client_id'],
            'numero'        => $data['numero'],
            'date_emission' => $data['date_emission'],
            'date_validite' => $data['date_validite'],
            'notes'         => $data['notes']
        ]);
        
        return $this->db->lastInsertId();
    }
}
