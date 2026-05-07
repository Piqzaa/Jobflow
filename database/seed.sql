-- Nettoyage avant insertion (optionnel, attention à l'ordre à cause des FK)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE facture_items;
TRUNCATE TABLE factures;
TRUNCATE TABLE devis_items;
TRUNCATE TABLE devis;
TRUNCATE TABLE clients;
TRUNCATE TABLE user_profiles;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Création d'un utilisateur de test
-- Password : 'password' (hashé avec BCRYPT)
INSERT INTO users (id, email, password, email_verified_at) VALUES 
(1, 'demo@jobflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- 2. Profil de l'utilisateur
INSERT INTO user_profiles (user_id, nom, prenom, entreprise, siret, adresse, code_postal, ville, telephone) VALUES 
(1, 'Dupont', 'Jean', 'Jean Dupont Freelance', '12345678901234', '10 rue de la Paix', '75001', 'Paris', '0102030405');

-- 3. Ajout de quelques clients
INSERT INTO clients (id, user_id, nom, email, telephone, adresse, code_postal, ville, siret) VALUES 
(1, 1, 'ACME Corp', 'contact@acme.com', '0600000001', '123 Avenue des Usines', '69000', 'Lyon', '98765432100015'),
(2, 1, 'Boulangerie Soleil', 'info@soleil-pain.fr', '0600000002', '5 Place du Marché', '33000', 'Bordeaux', '11223344556677');

-- 4. Un devis de test
INSERT INTO devis (id, user_id, client_id, numero, statut, date_emission, date_validite, montant_ht, montant_tva, montant_ttc) VALUES 
(1, 1, 1, 'DEV-2026-001', 'accepté', '2026-05-01', '2026-06-01', 1000.00, 200.00, 1200.00);

-- Items du devis
INSERT INTO devis_items (devis_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) VALUES 
(1, 'Création Site Web Vitrine', 1, 800.00, 20.00, 800.00, 960.00, 1),
(1, 'Maintenance (1 mois)', 1, 200.00, 20.00, 200.00, 240.00, 2);

-- 5. Une facture liée au devis
INSERT INTO factures (id, user_id, client_id, devis_id, numero, statut, date_emission, date_echeance, montant_ht, montant_tva, montant_ttc) VALUES 
(1, 1, 1, 1, 'FAC-2026-001', 'envoyée', '2026-05-07', '2026-06-07', 1000.00, 200.00, 1200.00);

-- Items de la facture (identiques au devis dans cet exemple)
INSERT INTO facture_items (facture_id, designation, quantite, prix_unitaire, tva, total_ht, total_ttc, position) VALUES 
(1, 'Création Site Web Vitrine', 1, 800.00, 20.00, 800.00, 960.00, 1),
(1, 'Maintenance (1 mois)', 1, 200.00, 20.00, 200.00, 240.00, 2);
