<?php

namespace App\Controllers;

use App\Models\Devis;
use App\Models\Client;

class DevisController {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $devisModel = new Devis();
        $clientModel = new Client();

        // Récupération des données
        $clients = $clientModel->getClients($userId);
        $devisList = $devisModel->getAllByUser($userId);
        
        render('devis', [
            'title'   => 'Mes Devis',
            'devis'   => $devisList,
            'clients' => $clients // Pour le select dans la modale
        ]);
    }
}
