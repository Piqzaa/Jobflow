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
    
    render('clients', [
      'title' => 'Mes Clients',
      'clients' => $clients
    ]);
  }

  // API : Récupère les données d'un client pour le modal
  public function get() {
    if (!isset($_SESSION['user_id'])) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'Session expirée']);
      exit;
    }

    $clientId = $_GET['id'] ?? null;
    $userId = $_SESSION['user_id'];
    
    $clientModel = new Client();
    $client = $clientModel->getClientById($clientId, $userId);

    header('Content-Type: application/json');
    echo json_encode([
      'success' => (bool)$client,
      'client' => $client,
      'error' => $client ? null : 'Client non trouvé'
    ]);
    exit;
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
    
    // Validation simple
    $errors = [];
    if (empty($inputs['nom'])) $errors[] = 'Nom requis.';
    if (empty($inputs['email']) || !filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if (strlen($siret) !== 14) $errors[] = 'SIRET invalide (14 chiffres).';

    if (!empty($errors)) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
      exit;
    }

    $clientModel = new Client();
    $result = $clientModel->createClient($_SESSION['user_id'], [
      'nom' => $inputs['nom'],
      'email' => $inputs['email'],
      'siret' => $siret,
      'adresse' => $inputs['adresse'] ?? null,
      'code_postal' => $inputs['code_postal'] ?? null,
      'ville' => $inputs['ville'] ?? null,
      'telephone' => $inputs['telephone'] ?? null,
      'notes' => $inputs['notes'] ?? null
    ]);

    header('Content-Type: application/json');
    echo json_encode([
      'success' => (bool)$result,
      'id' => $result,
      'error' => $result ? null : 'Erreur SQL lors de la création'
    ]);
    exit;
  }

  public function update() {
    if (!isset($_SESSION['user_id'])) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'Session expirée']);
      exit;
    }
    
    check_csrf($_POST['csrf_token'] ?? '');

    $clientId = $_POST['id'] ?? null;
    $inputs = array_map('trim', $_POST);
    $siret = str_replace(' ', '', $inputs['siret'] ?? '');
    
    if (empty($inputs['nom']) || strlen($siret) !== 14) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'Données invalides']);
      exit;
    }

    $clientModel = new Client();
    $result = $clientModel->updateClient($clientId, $_SESSION['user_id'], [
      'nom' => $inputs['nom'],
      'email' => $inputs['email'],
      'siret' => $siret,
      'adresse' => $inputs['adresse'] ?? null,
      'code_postal' => $inputs['code_postal'] ?? null,
      'ville' => $inputs['ville'] ?? null,
      'telephone' => $inputs['telephone'] ?? null,
      'notes' => $inputs['notes'] ?? null
    ]);

    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
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
    $clientModel = new Client();
    $result = $clientModel->deleteClient($clientId, $_SESSION['user_id']);

    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
  }
}
