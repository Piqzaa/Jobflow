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
    
}