<?php

namespace App\Tests\Helpers;

use App\Helpers\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires du Validator (logique pure, sans dépendances).
 */
class ValidatorTest extends TestCase {

    /* ------------------- email() ------------------- */

    public function testEmailValide() {
        $this->assertTrue(Validator::email('demo@jobflow.fr'));
    }

    public function testEmailAvecSousDomaines() {
        $this->assertTrue(Validator::email('contact@dev.jobflow.fr'));
    }

    public function testEmailInvalideSansArobase() {
        $this->assertFalse(Validator::email('demo-jobflow-fr'));
    }

    public function testEmailVideEstInvalide() {
        $this->assertFalse(Validator::email(''));
    }

    public function testEmailSansExtension() {
        $this->assertFalse(Validator::email('demo@jobflow'));
    }

    /* ------------------------ siret() ------------------------ */

    public function testSiretValide() {
        $this->assertTrue(Validator::siret('12345678900001'));
    }

    public function testSiretAvecSeparateurs() {
        // Les espaces/tirets/points sont nettoyés avant vérification
        $this->assertTrue(Validator::siret('123 456 789 00001'));
    }

    public function testSiretTropCourt() {
        $this->assertFalse(Validator::siret('123456'));
    }

    public function testSiretTropLong() {
        $this->assertFalse(Validator::siret('1234567890000123'));
    }

    public function testSiretContenantDesLettres() {
        $this->assertFalse(Validator::siret('123456789000A1'));
    }

    /* ------------------------ password() ------------------------ */

    public function testPasswordValide() {
        $this->assertTrue(Validator::password('Abcdefgh9'));
    }

    public function testPasswordTropCourt() {
        $this->assertFalse(Validator::password('Abcd1'));
    }

    public function testPasswordSansMajuscule() {
        $this->assertFalse(Validator::password('abcdef1234'));
    }

    public function testPasswordSansMinuscule() {
        $this->assertFalse(Validator::password('ABCDEF1234'));
    }

    public function testPasswordSansChiffre() {
        $this->assertFalse(Validator::password('Abcdefgh'));
    }

    /* ------------------------ phone() ------------------------ */

    public function testPhoneValideAvecEspaces() {
        $this->assertTrue(Validator::phone('06 12 34 56 78'));
    }

    public function testPhoneValideAvecPlus() {
        $this->assertTrue(Validator::phone('+33 6 12 34 56 78'));
    }

    public function testPhoneTropCourt() {
        $this->assertFalse(Validator::phone('06'));
    }

    public function testPhoneContenantDesLettres() {
        $this->assertFalse(Validator::phone('06 12 34 56 7A'));
    }

    /* ------------------------ required() ------------------------ */

    public function testRequiredAvecValeur() {
        $this->assertTrue(Validator::required('Jobflow'));
    }

    public function testRequiredAvecChaineVide() {
        $this->assertFalse(Validator::required(''));
    }

    public function testRequiredAvecEspaces() {
        $this->assertFalse(Validator::required('   '));
    }

    public function testRequiredAvecTableauVide() {
        $this->assertFalse(Validator::required([]));
    }

    public function testRequiredAvecZero() {
        $this->assertTrue(Validator::required(0));
    }

    /* ------------------------ positiveNumber() ------------------------ */

    public function testNombrePositif() {
        $this->assertTrue(Validator::positiveNumber(10));
    }

    public function testZeroEstPositif() {
        $this->assertTrue(Validator::positiveNumber(0));
    }

    public function testNombreNegatif() {
        $this->assertFalse(Validator::positiveNumber(-5));
    }

    public function testNombreNonNumerique() {
        $this->assertFalse(Validator::positiveNumber('abc'));
    }

    public function testDecimal() {
        $this->assertTrue(Validator::positiveNumber(19.99));
    }
}