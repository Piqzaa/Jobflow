<?php

namespace App\Helpers;

class Validator {
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function siret($siret) {
        $clean = str_replace([' ', '-', '.'], '', $siret);
        return strlen($clean) === 14 && ctype_digit($clean);
    }

    public static function password($password) {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        return true;
    }

    public static function required($value) {
        if (is_array($value)) return !empty($value);
        return trim((string)$value) !== '';
    }
}
