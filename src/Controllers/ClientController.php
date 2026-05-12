<?php

namespace App\Controllers;

use App\Models\Client;
use App\Config\Database;

class ClientController {
  public function index() {
    if (!isset($_SESSION['user_id'])) {
      header('Location: /login');
      exit;
    }

    $userId = $_SESSION['user_id'];

    $clientModel = new Client();
    $clients = $clientModel->getClients($userId);
    render('/clients', ['title' => 'Mes Clients', 'clients' => $clients]);
  }
  
  public function create() {
    if (!isset($_SESSION['user_id'])) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'Session expirée']);
      exit;
    }
    check_csrf($_POST['csrf_token'] ?? '');

    $inputs = array_map('trim', $_POST);
    $siret = str_replace(' ', '', $inputs['siret'] ?? '');
    $error = [];
    if (empty($inputs['nom'])) {
      $error[] = 'Le nom est requis.';
    }
    if (empty($inputs['email']) || !filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
      $error[] = 'Un email valide est requis.';
    }
    if (empty($inputs['siret']) || strlen($siret) !== 14) {
      $error[] = 'Le SIRET doit comporter exactement 14 caractères.';
    }

    if (!empty($error)) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => implode(' ', $error)]);
      exit;
    }

    $data = [
      'nom' => $inputs['nom'],
      'email' => $inputs['email'],
      'siret' => $siret,
      'adresse' => $inputs['adresse'] ?? null,
      'code_postal' => $inputs['code_postal'] ?? null,
      'ville' => $inputs['ville'] ?? null,
      'telephone' => $inputs['telephone'] ?? null,
      'notes' => $inputs['notes'] ?? null
    ];
    $userId = $_SESSION['user_id'];
    $clientModel = new Client();
    $result = $clientModel->createClient($userId, $data);

    header('Content-Type: application/json');
    if ($result) {
      echo json_encode(['success' => true, 'id' => $result]);
    } else {
      echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du client.']);
    }
    exit;
  }

  public function delete() {
    if (!isset($_SESSION['user_id'])) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'Session expirée']);
      exit;
    }
    check_csrf($_POST['csrf_token'] ?? '');
    
    $clientId = $_POST['id'] ?? null;
    $userId = $_SESSION['user_id'];
    if (!$clientId) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'ID du client manquant']);
      exit;
    }
    $clientModel = new Client();
    $result = $clientModel->deleteClient($clientId, $userId);

    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
  }
}