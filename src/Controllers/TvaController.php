<?php

namespace App\Controllers;

use App\Models\TvaPayment;

class TvaController {
    private $tvaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $this->tvaModel = new TvaPayment();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $year = date('Y');
        $factureModel = new \App\Models\Facture();
        $payments = $this->tvaModel->getAllByUser($userId);
        $tvaCollectee = $factureModel->getTotalTVAYear($userId, $year);
        $tvaPayee = $this->tvaModel->getTotalPaidYear($userId, $year);
        $tvaRestante = max(0, $tvaCollectee - $tvaPayee);
                render('tva', [
            'title' => 'Gestion de la TVA',
            'payments' => $payments,
            'tvaCollectee' => $tvaCollectee,
            'tvaPayee' => $tvaPayee,
            'tvaRestante' => $tvaRestante
        ]);
    }

    public function create() {
        check_csrf($_POST['csrf_token'] ?? '');
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $data = [
            'montant' => floatval($_POST['montant'] ?? 0),
            'date_paiement' => $_POST['date_paiement'] ?? date('Y-m-d'),
            'periode' => trim($_POST['periode'] ?? '')
        ];

        if ($data['montant'] <= 0) {
            echo json_encode(['success' => false, 'error' => 'Le montant doit être supérieur à 0.']);
            exit;
        }

        if ($this->tvaModel->create($userId, $data)) {
            $newId = \App\Config\Database::getInstance()->lastInsertId();
            $stats = $this->getUpdatedStats($userId);
            echo json_encode(['success' => true, 'id' => $newId, 'stats' => $stats]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement.']);
        }
        exit;
    }

    public function delete() {
        check_csrf($_POST['csrf_token'] ?? '');
        header('Content-Type: application/json');
        
        $id = $_POST['id'] ?? null;
        $userId = $_SESSION['user_id'];

        if ($id && $this->tvaModel->delete($id, $userId)) {
            $stats = $this->getUpdatedStats($userId);
            echo json_encode(['success' => true, 'stats' => $stats]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression.']);
        }
        exit;
    }

    private function getUpdatedStats($userId) {
        $year = date('Y');
        $factureModel = new \App\Models\Facture();
        $tvaCollectee = $factureModel->getTotalTVAYear($userId, $year);
        $tvaPayee = $this->tvaModel->getTotalPaidYear($userId, $year);
        $tvaRestante = max(0, $tvaCollectee - $tvaPayee);
        return [
            'collectee' => number_format($tvaCollectee, 2, ',', ' ') . ' €',
            'payee' => number_format($tvaPayee, 2, ',', ' ') . ' €',
            'restante' => number_format($tvaRestante, 2, ',', ' ') . ' €'
        ];
    }

}
