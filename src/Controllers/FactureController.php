<?php

namespace App\Controllers;

use App\Models\Facture;
use App\Models\Devis;
use App\Models\User;
use App\Helpers\MongoLogger;
use App\Helpers\Validator;

/**
 * Contrôleur pour la gestion des Factures
 */
class FactureController extends BaseController {

    public function index() {
        $this->checkAuth();

        $userId = $_SESSION['user_id'];
        $factureModel = new Facture();
        $clientModel = new \App\Models\Client();

        $clients = $clientModel->getClients($userId);
        $factures = $factureModel->getAllByUser($userId);
        $nextNumber = $factureModel->getNextNumber($userId);

        render('factures', [
            'title'      => 'Mes Factures',
            'factures'   => $factures,
            'clients'    => $clients,
            'nextNumber' => $nextNumber
        ]);
    }

    public function get() {
        $this->checkAuth();

        $factureId = $_GET['id'] ?? null;
        $factureModel = new Facture();
        $userId = $_SESSION['user_id'];
        $facture = $factureModel->getById($factureId, $userId);

        if (!$facture) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Facture non trouvée']);
            exit;
        }

        $items = $factureModel->getItems($factureId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'facture' => $facture,
            'items'   => $items
        ]);
        exit;
    }

    public function create() {
        $this->checkAuth();
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $factureModel = new Facture();
        $userModel = new User();

        $clientId      = !empty($_POST['client_id']) ? $_POST['client_id'] : null;
        $dateEmission  = !empty($_POST['date_emission']) ? $_POST['date_emission'] : date('Y-m-d');
        $dateEcheance  = !empty($_POST['date_echeance']) ? $_POST['date_echeance'] : date('Y-m-d', strtotime('+30 days'));
        $notes         = trim($_POST['notes'] ?? '');

        if (!$clientId) {
            echo json_encode(['success' => false, 'error' => 'Veuillez sélectionner un client.']);
            exit;
        }
        $tvaApp        = isset($_POST['tva_applicable']);
        $tvaRate       = $tvaApp ? 20.00 : 0.00;

        if ($tvaApp) {
            $userProfile = $userModel->findById($userId);
            if (empty($userProfile['tva_intra'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Numéro de TVA intracommunautaire requis dans votre profil pour appliquer la TVA.']);
                exit;
            }
        }

        $numero = $factureModel->getNextNumber($userId);
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

            if (!Validator::positiveNumber($qty) || !Validator::positiveNumber($p)) {
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

        $factureData = [
            'client_id'     => $clientId,
            'numero'        => $numero,
            'date_emission' => $dateEmission,
            'date_echeance' => $dateEcheance,
            'montant_ht'    => $totalHt,
            'montant_tva'   => $totalTva,
            'montant_ttc'   => $totalTtc,
            'tva_rate'      => $tvaRate,
            'notes'         => $notes
        ];

        $result = $factureModel->createWithItems($userId, $factureData, $items);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'id'      => $result,
            'error'   => $result ? null : 'Erreur lors de la sauvegarde de la facture'
        ]);
        exit;
    }

    public function update() {
        $this->checkAuth();
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $factureModel = new Facture();
        $factureId = $_POST['id'] ?? null;
        $userModel = new User();

        // Vérification statut
        $existing = $factureModel->getById($factureId, $userId);
        if (!$existing || $existing['statut'] !== 'brouillon') {
            echo json_encode(['success' => false, 'error' => 'Seule une facture en "Brouillon" peut être modifiée.']);
            exit;
        }

        $clientId     = $_POST['client_id'] ?? null;
        $dateEmission = $_POST['date_emission'] ?? null;
        $dateEcheance = $_POST['date_echeance'] ?? null;
        $notes        = trim($_POST['notes'] ?? '');
        $tvaApp       = isset($_POST['tva_applicable']);
        $tvaRate      = $tvaApp ? 20.00 : 0.00;

        if ($tvaApp) {
            $userProfile = $userModel->findById($userId);
            if (empty($userProfile['tva_intra'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Numéro de TVA intracommunautaire requis dans votre profil pour appliquer la TVA.']);
                exit;
            }
        }

        $designations = $_POST['item_designation'] ?? [];
        $quantites    = $_POST['item_quantite']    ?? [];
        $prix         = $_POST['item_prix']        ?? [];

        $items = [];
        $totalHt = 0;

        foreach ($designations as $i => $designation) {
            $qty  = $quantites[$i] ?? 0;
            $p    = $prix[$i] ?? 0;

            if (!Validator::positiveNumber($qty) || !Validator::positiveNumber($p)) {
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

        $factureData = [
            'client_id'     => $clientId,
            'date_emission' => $dateEmission,
            'date_echeance' => $dateEcheance,
            'montant_ht'    => $totalHt,
            'montant_tva'   => $totalTva,
            'montant_ttc'   => $totalTtc,
            'tva_rate'      => $tvaRate,
            'notes'         => $notes
        ];

        $result = $factureModel->updateWithItems($factureId, $userId, $factureData, $items);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'error'   => $result ? null : 'Erreur lors de la sauvegarde de la facture'
        ]);
        exit;
    }

    public function delete() {
        $this->checkAuth();
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $factureId = $_POST['id'] ?? null;

        $factureModel = new Facture();
        $result = $factureModel->deleteFacture($factureId, $userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'error'   => $result ? null : 'Erreur ou suppression interdite (Seuls les brouillons peuvent être supprimés).'
        ]);
        exit;
    }

    public function updateStatus() {
        $this->checkAuth();
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $factureId = $_POST['facture_id'] ?? null;
        $status = $_POST['status'] ?? null;

        $factureModel = new Facture();
        $result = $factureModel->updateStatus($factureId, $userId, $status);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$result,
            'error'   => $result ? null : 'Erreur lors de la mise à jour du statut'
        ]);
        exit;
    }

    public function createFromDevis() {
        $this->checkAuth();
        check_csrf($_POST['csrf_token'] ?? '');

        $userId = $_SESSION['user_id'];
        $devisId = $_POST['devis_id'] ?? null;

        if (!$devisId) {
            echo json_encode(['success' => false, 'error' => 'ID de devis manquant']);
            exit;
        }

        $devisModel = new Devis();
        $factureModel = new Facture();

        $devis = $devisModel->getById($devisId, $userId);
        if (!$devis) {
            echo json_encode(['success' => false, 'error' => 'Devis non trouvé']);
            exit;
        }

        if ($devis['statut'] !== 'accepte') {
            echo json_encode(['success' => false, 'error' => 'Seul un devis avec le statut "Accepté" peut être converti en facture.']);
            exit;
        }

        $items = $devisModel->getItems($devisId);
        $factureId = $factureModel->createFromDevis($userId, $devis, $items);

        if ($factureId) {
            MongoLogger::write(
                userId: $userId,
                action: 'convert_devis_to_facture',
                entity: 'facture',
                entityId: $factureId,
                data: ['devis_id' => $devisId]
            );

            echo json_encode(['success' => true, 'facture_id' => $factureId]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la conversion']);
        }
        exit;
    }

    public function pdf() {
        $this->checkAuth();

        $factureId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        $factureModel = new Facture();
        $userModel = new User();
        
        $facture = $factureModel->getById($factureId, $userId);

        if (!$facture) {
            echo "Facture non trouvée ou accès refusé.";
            exit;
        }

        $items = $factureModel->getItems($factureId);
        $userProfile = $userModel->findById($userId);

        ob_start();
        require __DIR__ . '/../Views/facture_pdf.php';
        $html = ob_get_clean();

        $pdfService = new \App\Services\PdfService();
        $filename = "Facture_{$facture['numero']}.pdf";
        $pdfService->generatePdf($html, $filename);
    }
}
