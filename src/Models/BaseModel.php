<?php

namespace App\Models;

use App\Config\Database;


abstract class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
