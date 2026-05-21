CREATE DATABASE IF NOT EXISTS jobflow 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE jobflow;

-- TABLE users : Authentification
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    verification_token VARCHAR(64) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE user_profiles : Informations entreprise/profil
CREATE TABLE user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    entreprise VARCHAR(255),
    siret VARCHAR(14),
    adresse TEXT,
    code_postal VARCHAR(10),
    ville VARCHAR(100),
    telephone VARCHAR(20),
    tva_intra VARCHAR(20),
    iban VARCHAR(34),
    bic VARCHAR(11),
    logo_filename VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE password_resets : Tokens reset password
CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE clients : Clients du freelance
CREATE TABLE clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    telephone VARCHAR(20),
    adresse TEXT,
    code_postal VARCHAR(10),
    ville VARCHAR(100),
    siret VARCHAR(14),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE devis : Devis/Estimations
CREATE TABLE devis (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    numero VARCHAR(50) NOT NULL UNIQUE,
    statut ENUM('brouillon', 'envoye', 'accepte', 'refuse', 'expire') DEFAULT 'brouillon',
    date_emission DATE NOT NULL,
    date_validite DATE,
    montant_ht DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    montant_tva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    montant_ttc DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_client_id (client_id),
    INDEX idx_statut (statut),
    INDEX idx_numero (numero),
    INDEX idx_date_emission (date_emission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE devis_items : Lignes de devis
CREATE TABLE devis_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    devis_id INT UNSIGNED NOT NULL,
    designation VARCHAR(255) NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    tva DECIMAL(5,2) NOT NULL,
    total_ht DECIMAL(10,2) NOT NULL,
    total_ttc DECIMAL(10,2) NOT NULL,
    position INT UNSIGNED NOT NULL DEFAULT 1,
    
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE,
    INDEX idx_devis_id (devis_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE factures : Factures
-- devis_id nullable car une facture PEUT être créée directement (client régulier, forfait mensuel)

CREATE TABLE factures (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    devis_id INT UNSIGNED NULL,  -- Nullable : facture directe possible
    numero VARCHAR(50) NOT NULL UNIQUE,
    statut ENUM('brouillon', 'envoyee', 'payee', 'en_retard', 'annulee') DEFAULT 'brouillon',
    date_emission DATE NOT NULL,
    date_echeance DATE NOT NULL,  -- Ex: +30 jours pour paiement
    date_paiement DATE NULL,      -- Date réelle du paiement
    montant_ht DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    montant_tva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    montant_ttc DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE SET NULL,
    -- SET NULL : si devis supprimé, facture reste mais perd la référence
    
    INDEX idx_user_id (user_id),
    INDEX idx_client_id (client_id),
    INDEX idx_devis_id (devis_id),
    INDEX idx_statut (statut),
    INDEX idx_numero (numero),
    INDEX idx_date_emission (date_emission),
    INDEX idx_date_echeance (date_echeance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE facture_items : Lignes de facture

CREATE TABLE facture_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    facture_id INT UNSIGNED NOT NULL,
    designation VARCHAR(255) NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    tva DECIMAL(5,2) NOT NULL,
    total_ht DECIMAL(10,2) NOT NULL,
    total_ttc DECIMAL(10,2) NOT NULL,
    position INT UNSIGNED NOT NULL DEFAULT 1,
    
    FOREIGN KEY (facture_id) REFERENCES factures(id) ON DELETE CASCADE,
    INDEX idx_facture_id (facture_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;