<?php

namespace App\Models;

use App\Config\Database;

class Client {
  private $db;

  public function __construct() {
    $this->db = Database::getInstance();
  }

  public function getClients($userId) {
  $stmt = $this->db->prepare("SELECT * FROM clients WHERE user_id = :user_id AND deleted_at IS NULL");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
  }

  public function createClient($userId, $data) {
    $stmt = $this->db->prepare("INSERT INTO clients (user_id, nom, email, siret, adresse, code_postal, ville, telephone, notes) VALUES (:user_id, :nom, :email, :siret, :adresse, :code_postal, :ville, :telephone, :notes)");
    return $stmt->execute([
      'user_id' => $userId,
      'nom' => $data['nom'] ?? null,
      'email' => $data['email'] ?? null,
      'siret' => $data['siret'] ?? null,
      'adresse' => $data['adresse'] ?? null,
      'code_postal' => $data['code_postal'] ?? null,
      'ville' => $data['ville'] ?? null,
      'telephone' => $data['telephone'] ?? null,
      'notes' => $data['notes'] ?? null
    ]);
  }

  public function deleteClient($clientId, $userId) {
    $stmt = $this->db->prepare("UPDATE clients SET deleted_at = NOW() WHERE id = :id AND user_id = :user_id");
    return $stmt->execute(['id' => $clientId, 'user_id' => $userId]);
  }
}