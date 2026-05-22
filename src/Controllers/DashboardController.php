<?php

namespace App\Controllers;

use App\Models\Facture;
use App\Models\User;
use App\Models\TvaPayment;
use App\Helpers\SecurityHelper;

class DashboardController {
    private $factureModel;
    private $tvaModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }
        $this->factureModel = new Facture();
        $this->tvaModel = new TvaPayment();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $year = date('Y');

        $caTotal = $this->factureModel->getTotalCAYear($userId, $year);
        $tvaCollectee = $this->factureModel->getTotalTVAYear($userId, $year);
        $tvaPayee = $this->tvaModel->getTotalPaidYear($userId, $year);
        
        $monthlyCA = $this->factureModel->getMonthlyCA($userId, $year);
        $monthlyTVA = $this->factureModel->getMonthlyTVA($userId, $year);
        $seuilMicro = 83600;
        $seuilTvaBase = 37500;
        $seuilTvaMajoré = 41250;

        $percentMicro = min(($caTotal / $seuilMicro) * 100, 100);
        
        // Détermination du statut TVA
        $tvaStatus = 'exo'; // exonéré par défaut
        if ($caTotal > $seuilTvaMajoré) {
            $tvaStatus = 'obligatoire_immediat';
        } elseif ($caTotal > $seuilTvaBase) {
            $tvaStatus = 'alerte_prochaine_annee';
        }

        $data = [
            'title' => 'Tableau de bord',
            'caTotal' => $caTotal,
            'tvaCollectee' => $tvaCollectee,
            'tvaPayee' => $tvaPayee,
            'tvaRestante' => max(0, $tvaCollectee - $tvaPayee),
            'monthlyCA' => json_encode(array_values($monthlyCA)),
            'monthlyTVA' => json_encode(array_values($monthlyTVA)),
            'percentMicro' => $percentMicro,
            'seuilMicro' => $seuilMicro,
            'seuilTvaBase' => $seuilTvaBase,
            'seuilTvaMajoré' => $seuilTvaMajoré,
            'tvaStatus' => $tvaStatus
        ];

        render('dashboard', $data);
    }
}
