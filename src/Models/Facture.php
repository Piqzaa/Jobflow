<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Facture {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllByUser($userId) {
        $sql = "SELECT f.*, c.nom as client_nom 
                FROM factures f 
                JOIN clients c ON f.client_id = c.id 
                WHERE f.user_id = :user_id 
                AND f.deleted_at IS NULL 
                ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId) {
        $sql = "SELECT f.*, c.nom as client_nom, c.adresse as client_adresse, 
                       c.code_postal as client_code_postal, c.ville as client_ville, c.siret as client_siret
                FROM factures f 
                JOIN clients c ON f.client_id = c.id 
                WHERE f.id = :id AND f.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }


    public function getItems($factureId) {
        $stmt = $this->db->prepare("SELECT * FROM facture_items WHERE facture_id = :facture_id ORDER BY position ASC");
        $stmt->execute(['facture_id' => $factureId]);
        return $stmt->fetchAll();
    }


    public function getNextNumber($userId) {
        $year = date('Y');
        $sql = "SELECT COUNT(*) as total FROM factures WHERE user_id = :user_id AND YEAR(created_at) = :year";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'year' => $year]);
        $count = $stmt->fetch()['total'] + 1;
        
        return "FAC-" . $year . "-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }


    public function createFromDevis($userId, $devis, $items) {
        try {
            $this->db->beginTransaction();

            $numero = $this->getNextNumber($userId);
            $dateEcheance = date('Y-m-d', strtotime('+30 days'));

            $sqlFacture = "INSERT INTO factures (user_id, client_id, devis_id, numero, statut, date_emission, date_echeance, montant_ht, montant_tva, montant_ttc, notes) 
                           VALUES (:user_id, :client_id, :devis_id, :numero, 'brouillon', NOW(), :date_echeance, :ht, :tva, :ttc, :notes)";
            
            $stmt = $this->db->prepare($sqlFacture);
            $stmt->execute([
                'user_id'       => $userId,
                'client_id'     => $devis['client_id'],
                'devis_id'      => $devis['id'],
                'numero'        => $numero,
                'date_echeance' => $dateEcheance,
                'ht'            => $devis['montant_ht'],
                'tva'           => $devis['montant_tva'],
                'ttc'           => $devis['montant_ttc'],
                'notes'         => $devis['notes']
            ]);

            $factureId = $this->db->lastInsertId();
            $sqlItem = "INSERT INTO facture_items (facture_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) 
                        VALUES (:facture_id, :designation, :qty, :prix, :tva_rate, :tht, :tttc, :pos)";
            
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                $stmtItem->execute([
                    'facture_id'  => $factureId,
                    'designation' => $item['designation'],
                    'qty'         => $item['quantite'],
                    'prix'        => $item['prix_unitaire'],
                    'tva_rate'    => $item['tva'],
                    'tht'         => $item['total_ht'],
                    'tttc'        => $item['total_ttc'],
                    'pos'         => $item['position']
                ]);
            }

            $this->db->commit();
            return $factureId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("[FactureModel] Erreur conversion : " . $e->getMessage());
            return false;
        }
    }

    public function createWithItems($userId, $factureData, $items) {
        try {
            $this->db->beginTransaction();

            $sqlFacture = "INSERT INTO factures (user_id, client_id, numero, statut, date_emission, date_echeance, montant_ht, montant_tva, montant_ttc, notes) 
                           VALUES (:user_id, :client_id, :numero, :statut, :date_emission, :date_echeance, :ht, :tva, :ttc, :notes)";
            
            $stmt = $this->db->prepare($sqlFacture);
            $stmt->execute([
                'user_id'       => $userId,
                'client_id'     => $factureData['client_id'],
                'numero'        => $factureData['numero'],
                'statut'        => $factureData['statut'] ?? 'brouillon',
                'date_emission' => $factureData['date_emission'],
                'date_echeance' => $factureData['date_echeance'],
                'ht'            => $factureData['montant_ht'],
                'tva'           => $factureData['montant_tva'],
                'ttc'           => $factureData['montant_ttc'],
                'notes'         => $factureData['notes']
            ]);

            $factureId = $this->db->lastInsertId();
            $sqlItem = "INSERT INTO facture_items (facture_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) 
                        VALUES (:facture_id, :designation, :qty, :prix, :tva_rate, :tht, :tttc, :pos)";
            
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $index => $item) {
                $stmtItem->execute([
                    'facture_id'  => $factureId,
                    'designation' => $item['designation'],
                    'qty'         => $item['qty'],
                    'prix'        => $item['prix'],
                    'tva_rate'    => $factureData['tva_rate'],
                    'tht'         => $item['total_ht'],
                    'tttc'        => $item['total_ttc'],
                    'pos'         => $index + 1
                ]);
            }

            $this->db->commit();
            return $factureId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("[FactureModel] Erreur création manuelle : " . $e->getMessage());
            return false;
        }
    }

    public function updateWithItems($factureId, $userId, $factureData, $items) {
        try {
            $this->db->beginTransaction();

            $sqlFacture = "UPDATE factures SET client_id = :client_id, date_emission = :date_emission, date_echeance = :date_echeance, montant_ht = :ht, montant_tva = :tva, montant_ttc = :ttc, notes = :notes WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($sqlFacture);
            $stmt->execute([
                'id'            => $factureId,
                'user_id'       => $userId,
                'client_id'     => $factureData['client_id'],
                'date_emission' => $factureData['date_emission'],
                'date_echeance' => $factureData['date_echeance'],
                'ht'            => $factureData['montant_ht'],
                'tva'           => $factureData['montant_tva'],
                'ttc'           => $factureData['montant_ttc'],
                'notes'         => $factureData['notes']
            ]);

            $stmtDelete = $this->db->prepare("DELETE FROM facture_items WHERE facture_id = :facture_id");
            $stmtDelete->execute(['facture_id' => $factureId]);

            $sqlItem = "INSERT INTO facture_items (facture_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) 
                        VALUES (:facture_id, :designation, :qty, :prix, :tva_rate, :tht, :tttc, :pos)";
            
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $index => $item) {
                $stmtItem->execute([
                    'facture_id'  => $factureId,
                    'designation' => $item['designation'],
                    'qty'         => $item['qty'],
                    'prix'        => $item['prix'],
                    'tva_rate'    => $factureData['tva_rate'],
                    'tht'         => $item['total_ht'],
                    'tttc'        => $item['total_ttc'],
                    'pos'         => $index + 1
                ]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("[FactureModel] Erreur update : " . $e->getMessage());
            return false;
        }
    }

    public function deleteFacture($id, $userId) {
        $stmt = $this->db->prepare("UPDATE factures SET deleted_at = NOW() WHERE id = :id AND user_id = :user_id AND statut = 'brouillon'");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function updateStatus($id, $userId, $status) {
        $allowedStatuses = ['brouillon', 'envoyee', 'payee', 'annulee'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $sql = "UPDATE factures SET statut = :status";
        $params = [
            'status'  => $status,
            'id'      => $id,
            'user_id' => $userId
        ];

        if ($status === 'payee') {
            $sql .= ", date_paiement = IFNULL(date_paiement, NOW())";
        }

        $sql .= " WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getTotalCAYear($userId, $year) {
        $sql = "SELECT SUM(montant_ht) as total 
                FROM factures 
                WHERE user_id = :user_id 
                AND statut = 'payee' 
                AND YEAR(date_paiement) = :year 
                AND deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'year' => $year]);
        $result = $stmt->fetch();
        
        return (float)($result['total'] ?? 0);
    }

    public function getMonthlyCA($userId, $year) {
        $sql = "SELECT MONTH(date_paiement) as mois, SUM(montant_ht) as total 
                FROM factures 
                WHERE user_id = :user_id 
                AND statut = 'payee' 
                AND YEAR(date_paiement) = :year 
                AND deleted_at IS NULL 
                GROUP BY MONTH(date_paiement) 
                ORDER BY mois ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'year' => $year]);
        $rows = $stmt->fetchAll();

        // On initialise un tableau avec les 12 mois à 0
        $monthlyData = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $monthlyData[(int)$row['mois']] = (float)$row['total'];
        }
        
        return $monthlyData;
    }

    public function getTotalTVAYear($userId, $year) {
        $sql = "SELECT SUM(montant_tva) as total 
                FROM factures 
                WHERE user_id = :user_id 
                AND statut = 'payee' 
                AND YEAR(date_paiement) = :year 
                AND deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'year' => $year]);
        $result = $stmt->fetch();
        
        return (float)($result['total'] ?? 0);
    }

    public function getMonthlyTVA($userId, $year) {
        $sql = "SELECT MONTH(date_paiement) as mois, SUM(montant_tva) as total 
                FROM factures 
                WHERE user_id = :user_id 
                AND statut = 'payee' 
                AND YEAR(date_paiement) = :year 
                AND deleted_at IS NULL 
                GROUP BY MONTH(date_paiement) 
                ORDER BY mois ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'year' => $year]);
        $rows = $stmt->fetchAll();

        $monthlyData = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $monthlyData[(int)$row['mois']] = (float)$row['total'];
        }
        
        return $monthlyData;
    }
}
