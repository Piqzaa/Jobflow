<?php

namespace App\Tests\Models;

use App\Models\Devis;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Fake statement qui renvoie une ligne attendue, sans requête réelle.
 */
class FakeStatement {
    public $rows = [];
    public function execute($params = []) { return true; }
    public function fetch() { return array_shift($this->rows) ?: false; }
}

/**
 * Fake PDO : isole le modèle de la vraie base MySQL.
 */
class FakePdo {
    public $statement;
    public function prepare($sql) { return $this->statement; }
}

/**
 * Tests du calcul du prochain numéro de devis.
 */
class DevisNumberingTest extends TestCase {

    private function devisWithFake(?string $lastNumeroOrNull) {
        $stmt = new FakeStatement();
        $stmt->rows = $lastNumeroOrNull ? [['numero' => $lastNumeroOrNull]] : [];

        $pdo = new FakePdo();
        $pdo->statement = $stmt;

        // Construit sans exécuter le constructeur (qui se connecte à MySQL).
        $devis = (new ReflectionClass(Devis::class))->newInstanceWithoutConstructor();
        $prop = new \ReflectionProperty(Devis::class, 'db');
        $prop->setAccessible(true);
        $prop->setValue($devis, $pdo);
        return $devis;
    }

    public function testPremierDevisDeLAnnee() {
        $devis = $this->devisWithFake(null);
        $this->assertSame('DEV-' . date('Y') . '-001', $devis->getNextNumber(1));
    }

    public function testIncrementeLeDernierNumero() {
        $devis = $this->devisWithFake('DEV-' . date('Y') . '-005');
        $this->assertSame('DEV-' . date('Y') . '-006', $devis->getNextNumber(1));
    }

    public function testIncrementeApres99() {
        $devis = $this->devisWithFake('DEV-' . date('Y') . '-099');
        $this->assertSame('DEV-' . date('Y') . '-100', $devis->getNextNumber(1));
    }
}