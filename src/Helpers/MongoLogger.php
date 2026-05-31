<?php

namespace App\Helpers;

use App\Config\MongoDB;
use Exception;

class MongoLogger {
    public static function write(
        ?int $userId,
        string $action,
        string $entity,
        ?int $entityId = null,
        array $data = []
    ): void {
        try {
            $db = MongoDB::getInstance();
            
            // Si la DB est nulle (extension manquante ou erreur de connexion), on ignore silencieusement
            if (!$db) {
                return;
            }

            $collection = $db->activity_logs;

            $collection->insertOne([
                'user_id'    => $userId,
                'action'     => $action,
                'entity'     => $entity,
                'entity_id'  => $entityId,
                'data'       => $data,
                'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
                'created_at' => new \MongoDB\BSON\UTCDateTime(new \DateTime()),
            ]);
        } catch (Exception $e) {
            error_log("[MongoLogger] Erreur : " . $e->getMessage());
        }
    }
}