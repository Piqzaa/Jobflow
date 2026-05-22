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

        $userId = $_SESSION['user_id'];
        $data = [
            'montant' => floatval($_POST['montant'] ?? 0),
            'date_paiement' => $_POST['date_paiement'] ?? date('Y-m-d'),
            'periode' => trim($_POST['periode'] ?? '')
        ];

        if ($data['montant'] <= 0) {
            render('tva', [
                'title' => 'Gestion de la TVA',
                'payments' => $this->tvaModel->getAllByUser($userId),
                'error' => 'Le montant doit être supérieur à 0.'
            ]);
            return;
        }

        if ($this->tvaModel->create($userId, $data)) {
            header('Location: ' . url('/tva'));
            exit;
        } else {
            render('tva', [
                'title' => 'Gestion de la TVA',
                'payments' => $this->tvaModel->getAllByUser($userId),
                'error' => 'Erreur lors de l\'enregistrement du paiement.'
            ]);
        }
    }

    public function delete() {
        check_csrf($_POST['csrf_token'] ?? '');
        
        $id = $_POST['id'] ?? null;
        $userId = $_SESSION['user_id'];

        if ($id && $this->tvaModel->delete($id, $userId)) {
            header('Location: ' . url('/tva'));
            exit;
        }
        
        header('Location: ' . url('/tva'));
        exit;
    }
}
