<?php

namespace App\Services;


/**
 * Centralise le calcul des montants HT/TVA/TTC pour les documents
 * (devis et factures). Logique pure et testable.
 */
class TvaCalculator {

    /**
     * Calcule les montants d'une ligne d'article.
     *
     * @param float $qty Quantité
     * @param float $prix Prix unitaire HT
     * @param float $tvaRate Taux de TVA en pourcentage (0 ou 20)
     * @return array{total_ht: float, total_ttc: float}
     */
    public static function calculateLine($qty, $prix, $tvaRate) {
        $qty = (float)$qty;
        $prix = (float)$prix;

        $totalHt = $qty * $prix;
        $totalTtc = $totalHt * (1 + ((float)$tvaRate / 100));

        return [
            'total_ht'  => $totalHt,
            'total_ttc' => $totalTtc,
        ];
    }

    /**
     * Calcule les totaux globaux HT, TVA et TTC d'un ensemble de lignes.
     *
     * @param array $lines Liste de lignes, chacune contenant total_ht et total_ttc
     * @param float $tvaRate Taux de TVA en pourcentage
     * @return array{montant_ht: float, montant_tva: float, montant_ttc: float}
     */
    public static function calculateTotals($lines, $tvaRate) {
        $totalHt = 0.0;
        $totalTtc = 0.0;

        foreach ($lines as $line) {
            $totalHt += (float)($line['total_ht'] ?? 0);
            $totalTtc += (float)($line['total_ttc'] ?? 0);
        }

        $totalTva = $totalHt * ((float)$tvaRate / 100);

        return [
            'montant_ht'  => $totalHt,
            'montant_tva' => $totalTva,
            'montant_ttc' => $totalTtc,
        ];
    }
}
