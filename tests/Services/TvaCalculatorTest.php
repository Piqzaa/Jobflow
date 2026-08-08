<?php

namespace App\Tests\Services;

use App\Services\TvaCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires du calcul HT / TVA / TTC (cœur métier de Jobflow).
 */
class TvaCalculatorTest extends TestCase {

    /* ------------------------ calculateLine ------------------------ */

    public function testLigneSansTva() {
        $line = TvaCalculator::calculateLine(2, 100, 0);
        $this->assertEquals(200, $line['total_ht']);
        $this->assertEquals(200, $line['total_ttc']);
    }

    public function testLigneAvecTva20() {
        $line = TvaCalculator::calculateLine(2, 100, 20);
        $this->assertEquals(200, $line['total_ht']);
        $this->assertEquals(240, $line['total_ttc']);
    }

    public function testUneLigneUnArticle() {
        $line = TvaCalculator::calculateLine(1, 50, 20);
        $this->assertEquals(50, $line['total_ht']);
        $this->assertEquals(60, $line['total_ttc']);
    }

    public function testQuantiteZero() {
        $line = TvaCalculator::calculateLine(0, 100, 20);
        $this->assertEquals(0, $line['total_ht']);
        $this->assertEquals(0, $line['total_ttc']);
    }

    public function testPrixNul() {
        $line = TvaCalculator::calculateLine(3, 0, 20);
        $this->assertEquals(0, $line['total_ht']);
        $this->assertEquals(0, $line['total_ttc']);
    }

    public function testAcceptsChainesNumeriques() {
        $line = TvaCalculator::calculateLine('2', '100', 20);
        $this->assertEquals(200, $line['total_ht']);
        $this->assertEquals(240, $line['total_ttc']);
    }

    /* ------------------------ calculateTotals ------------------------ */

    public function testTotauxMultiLignesAvecTva() {
        $lignes = [
            ['total_ht' => 100, 'total_ttc' => 120],
            ['total_ht' => 200, 'total_ttc' => 240],
            ['total_ht' => 50,  'total_ttc' => 60],
        ];
        $result = TvaCalculator::calculateTotals($lignes, 20);

        $this->assertEquals(350, $result['montant_ht']);
        $this->assertEquals(70,  $result['montant_tva']);
        $this->assertEquals(420, $result['montant_ttc']);
    }

    public function testTotauxSansTva() {
        $lignes = [
            ['total_ht' => 100, 'total_ttc' => 100],
            ['total_ht' => 50,  'total_ttc' => 50],
        ];
        $result = TvaCalculator::calculateTotals($lignes, 0);

        $this->assertEquals(150, $result['montant_ht']);
        $this->assertEquals(0,   $result['montant_tva']);
        $this->assertEquals(150, $result['montant_ttc']);
    }

    public function testTotauxAvecLignesVides() {
        $result = TvaCalculator::calculateTotals([], 20);

        $this->assertEquals(0, $result['montant_ht']);
        $this->assertEquals(0, $result['montant_tva']);
        $this->assertEquals(0, $result['montant_ttc']);
    }

    public function testTotauxAvecLigneManquante() {
        $lignes = [
            ['total_ht' => 100, 'total_ttc' => 120],
            ['total_ttc' => 60],
        ];
        $result = TvaCalculator::calculateTotals($lignes, 20);

        $this->assertEquals(100, $result['montant_ht']);
        $this->assertEquals(180, $result['montant_ttc']);
        $this->assertEquals(20,  $result['montant_tva']);
    }

    public function testCoherenceHtPlusTvaEgalTtc() {
        $lignes = [];
        for ($i = 1; $i <= 50; $i++) {
            $line = TvaCalculator::calculateLine($i, 7, 20);
            $lignes[] = $line;
        }
        $r = TvaCalculator::calculateTotals($lignes, 20);
        $this->assertEqualsWithDelta($r['montant_ht'] + $r['montant_tva'], $r['montant_ttc'], 0.000001);
    }
}