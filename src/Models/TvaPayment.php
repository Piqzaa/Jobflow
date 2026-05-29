<?php

namespace App\Models;

use App\Config\Database;

class TvaPayment extends BaseModel {

    public function getAllByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM tva_payments WHERE user_id = ? AND deleted_at IS NULL ORDER BY date_paiement DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create($userId, $data) {
        $sql = "INSERT INTO tva_payments (user_id, montant, date_paiement, periode) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $data['montant'],
            $data['date_paiement'],
            $data['periode'] ?? null
        ]);
    }

    public function delete($id, $userId) {
        $stmt = $this->db->prepare("UPDATE tva_payments SET deleted_at = NOW() WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function getTotalPaidYear($userId, $year) {
        $stmt = $this->db->prepare("SELECT SUM(montant) as total FROM tva_payments WHERE user_id = ? AND YEAR(date_paiement) = ? AND deleted_at IS NULL");
        $stmt->execute([$userId, $year]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
}
