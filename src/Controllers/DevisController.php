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

        $clients = $clientModel->getClients($userId);
        $devisList = $devisModel->getAllByUser($userId);
        $nextNumber = $devisModel->getNextNumber($userId);
        
        render('devis', [
            'title'      => 'Mes Devis',
            'devis'      => $devisList,
            'clients'    => $clients,
            'nextNumber' => $nextNumber
        ]);
    }

    public function get() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Session expirée']);
            exit;
        }

        $devisId = $_GET['id'] ?? null;
        $devisModel = new Devis();
        $userId = $_SESSION['user_id'];
        $devis = $devisModel->getById($devisId, $userId);

        if (!$devis) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Devis non trouvé']);
            exit;
        }

        $items = $devisModel->getItems($devisId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'devis'   => $devis,
            'items'   => $items
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

        $userId = $_SESSION['user_id'];
        $devisModel = new Devis();
        $userModel = new \App\Models\User();

        $clientId     = $_POST['client_id'] ?? null;
        $dateEmission = $_POST['date_emission'] ?? date('Y-m-d');
        $dateValidite = $_POST['date_validite'] ?? null;
        $notes        = trim($_POST['notes'] ?? '');
        $tvaApp       = isset($_POST['tva_applicable']);
        $tvaRate      = $tvaApp ? 20.00 : 0.00;

        // Vérification TVA Intracommunautaire si TVA applicable
        if ($tvaApp) {
            $userProfile = $userModel->findById($userId);
            if (empty($userProfile['tva_intra'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'error' => 'Vous devez renseigner votre numéro de TVA intracommunautaire dans votre profil pour créer un devis avec TVA.'
                ]);
                exit;
            }
        }

        $numero = $devisModel->getNextNumber($userId);

        $designations = $_POST['item_designation'] ?? [];
        $quantites    = $_POST['item_quantite']    ?? [];
        $prix         = $_POST['item_prix']        ?? [];

        if (empty($designations)) {
            echo json_encode(['success' => false, 'error' => 'Au moins un article requis.']);
            exit;
        }

        $items = [];
        $totalHt = 0;

        foreach ($designations as $i => $designation) {
            $qty  = $quantites[$i] ?? 0;
            $p    = $prix[$i] ?? 0;

            if (!\App\Helpers\Validator::positiveNumber($qty) || !\App\Helpers\Validator::positiveNumber($p)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Les quantités et les prix doivent être des nombres positifs.']);
                exit;
            }

            $qty  = floatval($qty);
            $p    = floatval($p);
            $tht  = $qty * $p;
            $tttc = $tht * (1 + ($tvaRate / 100));

            $items[] = [
                'designation' => trim($designation),
                'qty'         => $qty,
                'prix'        => $p,
                'total_ht'    => $tht,
                'total_ttc'   => $tttc
            ];
            $totalHt += $tht;
        }

        $totalTva = $totalHt * ($tvaRate / 100);
        $totalTtc = $totalHt + $totalTva;

        $devisData = [
            'client_id'     => $clientId,
            'numero'        => $numero,
            'date_emission' => $dateEmission,
            'date_validite' => $dateValidite,
            'montant_ht'    => $totalHt,
            'montant_tva'   => $totalTva,
            'montant_ttc'   => $totalTtc,
            'tva_rate'      => $tvaRate,
            'notes'         => $notes
        ];

        $result = $devisModel->createWithItems($userId, $devisData, $items);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'id'      => $result,
            'error'   => $result ? null : 'Erreur lors de la sauvegarde du devis'
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

        $userId = $_SESSION['user_id'];
        $devisModel = new Devis();

        $devisId      = $_POST['id'] ?? null;
        $clientId     = $_POST['client_id'] ?? null;
        $dateEmission = $_POST['date_emission'] ?? null;
        $dateValidite = $_POST['date_validite'] ?? null;
        $notes        = trim($_POST['notes'] ?? '');
        $tvaApp       = isset($_POST['tva_applicable']);
        $tvaRate      = $tvaApp ? 20.00 : 0.00;

        $designations = $_POST['item_designation'] ?? [];
        $quantites    = $_POST['item_quantite']    ?? [];
        $prix         = $_POST['item_prix']        ?? [];

        if (empty($designations)) {
            echo json_encode(['success' => false, 'error' => 'Au moins un article requis.']);
            exit;
        }

        $items = [];
        $totalHt = 0;

        foreach ($designations as $i => $designation) {
            $qty  = $quantites[$i] ?? 0;
            $p    = $prix[$i] ?? 0;

            if (!\App\Helpers\Validator::positiveNumber($qty) || !\App\Helpers\Validator::positiveNumber($p)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Les quantités et les prix doivent être des nombres positifs.']);
                exit;
            }

            $qty  = floatval($qty);
            $p    = floatval($p);
            $tht  = $qty * $p;
            $tttc = $tht * (1 + ($tvaRate / 100));

            $items[] = [
                'designation' => trim($designation),
                'qty'         => $qty,
                'prix'        => $p,
                'total_ht'    => $tht,
                'total_ttc'   => $tttc
            ];
            $totalHt += $tht;
        }

        $totalTva = $totalHt * ($tvaRate / 100);
        $totalTtc = $totalHt + $totalTva;

        $devisData = [
            'client_id'     => $clientId,
            'date_emission' => $dateEmission,
            'date_validite' => $dateValidite,
            'montant_ht'    => $totalHt,
            'montant_tva'   => $totalTva,
            'montant_ttc'   => $totalTtc,
            'tva_rate'      => $tvaRate,
            'notes'         => $notes
        ];

        $result = $devisModel->updateWithItems($devisId, $userId, $devisData, $items);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'error'   => $result ? null : 'Erreur lors de la sauvegarde du devis'
        ]);
        exit;
    }

    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Session expirée']);
            exit;
        }

        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $devisId = $_POST['devis_id'] ?? null;

        if (!$devisId) {
            echo json_encode(['success' => false, 'error' => 'ID de devis manquant']);
            exit;
        }

        $devisModel = new Devis();
        $result = $devisModel->deleteDevis($devisId, $userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'error'   => $result ? null : 'Erreur lors de la suppression du devis'
        ]);
        exit;
    }

    public function pdf() {
        $this->checkAuth();

        $devisId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        $devisModel = new Devis();
        $userModel = new \App\Models\User();
        $devis = $devisModel->getById($devisId, $userId);

        if (!$devis) {
            echo "Devis non trouvé ou accès refusé.";
            exit;
        }

        $items = $devisModel->getItems($devisId);
        $userProfile = $userModel->findById($userId);

        ob_start();
        require __DIR__ . '/../Views/devis_pdf.php';
        $html = ob_get_clean();

        $pdfService = new \App\Services\PdfService();
        $filename = "Devis_{$devis['numero']}.pdf";
        $pdfService->generatePdf($html, $filename);
    }

    public function updateStatus() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Session expirée']);
            exit;
        }

        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $devisId = $_POST['devis_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$devisId || !$status) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Données manquantes']);
            exit;
        }

        $devisModel = new Devis();
        $result = $devisModel->updateStatus($devisId, $userId, $status);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'error'   => $result ? null : 'Erreur lors de la mise à jour du statut'
        ]);
        exit;
    }
}
      exit;
    }
}
