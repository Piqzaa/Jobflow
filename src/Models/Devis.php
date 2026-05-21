<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Devis {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getItems($devisId) {
        $stmt = $this->db->prepare("SELECT * FROM devis_items WHERE devis_id = :devis_id ORDER BY position ASC");
        $stmt->execute(['devis_id' => $devisId]);
        return $stmt->fetchAll();
    }

    public function getAllByUser($userId) {
        $sql = "SELECT d.*, c.nom as client_nom, c.adresse as client_adresse, 
                       c.code_postal as client_code_postal, c.ville as client_ville, c.siret as client_siret
                FROM devis d 
                JOIN clients c ON d.client_id = c.id 
                WHERE d.user_id = :user_id 
                AND d.deleted_at IS NULL 
                ORDER BY d.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId) {
        $sql = "SELECT d.*, c.nom as client_nom, c.adresse as client_adresse, 
                       c.code_postal as client_code_postal, c.ville as client_ville, c.siret as client_siret
                FROM devis d 
                JOIN clients c ON d.client_id = c.id 
                WHERE d.id = :id AND d.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }


    public function getNextNumber($userId) {
        $year = date('Y');
        $sql = "SELECT COUNT(*) as total FROM devis WHERE user_id = :user_id AND YEAR(created_at) = :year";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'year' => $year]);
        $count = $stmt->fetch()['total'] + 1;
        
        return "DEV-" . $year . "-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function createWithItems($userId, $devisData, $items) {
        try {
            $this->db->beginTransaction();

            $sqlDevis = "INSERT INTO devis (user_id, client_id, numero, date_emission, date_validite, montant_ht, montant_tva, montant_ttc, notes) 
                         VALUES (:user_id, :client_id, :numero, :date_emission, :date_validite, :ht, :tva, :ttc, :notes)";
            
            $stmt = $this->db->prepare($sqlDevis);
            $stmt->execute([
                'user_id'       => $userId,
                'client_id'     => $devisData['client_id'],
                'numero'        => $devisData['numero'],
                'date_emission' => $devisData['date_emission'],
                'date_validite' => $devisData['date_validite'],
                'ht'            => $devisData['montant_ht'],
                'tva'           => $devisData['montant_tva'],
                'ttc'           => $devisData['montant_ttc'],
                'notes'         => $devisData['notes']
            ]);

            $devisId = $this->db->lastInsertId();
            $sqlItem = "INSERT INTO devis_items (devis_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) 
                        VALUES (:devis_id, :designation, :qty, :prix, :tva_rate, :tht, :tttc, :pos)";
            
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $index => $item) {
                $stmtItem->execute([
                    'devis_id'    => $devisId,
                    'designation' => $item['designation'],
                    'qty'         => $item['qty'],
                    'prix'        => $item['prix'],
                    'tva_rate'    => $devisData['tva_rate'],
                    'tht'         => $item['total_ht'],
                    'tttc'        => $item['total_ttc'],
                    'pos'         => $index + 1
                ]);
            }

            $this->db->commit();
            return $devisId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("[DevisModel] Erreur transaction : " . $e->getMessage());
            return false;
        }
    }

    public function updateWithItems($devisId, $userId, $devisData, $items) {
        try {
            $this->db->beginTransaction();

            $sqlDevis = "UPDATE devis SET client_id = :client_id, date_emission = :date_emission, date_validite = :date_validite, montant_ht = :ht, montant_tva = :tva, montant_ttc = :ttc, notes = :notes WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($sqlDevis);
            $stmt->execute([
                'id'            => $devisId,
                'user_id'       => $userId,
                'client_id'     => $devisData['client_id'],
                'date_emission' => $devisData['date_emission'],
                'date_validite' => $devisData['date_validite'],
                'ht'            => $devisData['montant_ht'],
                'tva'           => $devisData['montant_tva'],
                'ttc'           => $devisData['montant_ttc'],
                'notes'         => $devisData['notes']
            ]);

            $stmtDelete = $this->db->prepare("DELETE FROM devis_items WHERE devis_id = :devis_id");
            $stmtDelete->execute(['devis_id' => $devisId]);

            $sqlItem = "INSERT INTO devis_items (devis_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) 
                        VALUES (:devis_id, :designation, :qty, :prix, :tva_rate, :tht, :tttc, :pos)";
            
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $index => $item) {
                $stmtItem->execute([
                    'devis_id'    => $devisId,
                    'designation' => $item['designation'],
                    'qty'         => $item['qty'],
                    'prix'        => $item['prix'],
                    'tva_rate'    => $devisData['tva_rate'],
                    'tht'         => $item['total_ht'],
                    'tttc'        => $item['total_ttc'],
                    'pos'         => $index + 1
                ]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("[DevisModel] Erreur transaction : " . $e->getMessage());
            return false;
        }
    }

    public function deleteDevis($id, $userId) {
        $stmt = $this->db->prepare("UPDATE devis SET deleted_at = NOW() WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}
