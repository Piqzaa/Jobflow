<?php

namespace App\Models;

use App\Config\Database;

class Client extends BaseModel {

  public function getClients($userId) {
    $stmt = $this->db->prepare("SELECT * FROM clients WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
  }

  public function getClientById($clientId, $userId) {
    $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL");
    $stmt->execute(['id' => $clientId, 'user_id' => $userId]);
    return $stmt->fetch();
  }

  public function createClient($userId, $data) {
    $stmt = $this->db->prepare("INSERT INTO clients (user_id, nom, email, siret, adresse, code_postal, ville, telephone, notes) VALUES (:user_id, :nom, :email, :siret, :adresse, :code_postal, :ville, :telephone, :notes)");
    $result = $stmt->execute([
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
    return $result ? $this->db->lastInsertId() : false;
  }

  public function deleteClient($clientId, $userId) {
    $stmt = $this->db->prepare("UPDATE clients SET deleted_at = NOW() WHERE id = :id AND user_id = :user_id");
    return $stmt->execute(['id' => $clientId, 'user_id' => $userId]);
  }

  public function updateClient($clientId, $userId, $data) {
    $stmt = $this->db->prepare("UPDATE clients SET nom = :nom, email = :email, siret = :siret, adresse = :adresse, code_postal = :code_postal, ville = :ville, telephone = :telephone, notes = :notes, updated_at = NOW() WHERE id = :id AND user_id = :user_id");
    return $stmt->execute([
      'id' => $clientId,
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
}