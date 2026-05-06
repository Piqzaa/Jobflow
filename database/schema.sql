-- Création de la base de données
CREATE DATABASE IF NOT EXISTS jobflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jobflow;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    entreprise VARCHAR(255),
    siret VARCHAR(14),
    adresse TEXT,
    telephone VARCHAR(20),
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des clients
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    telephone VARCHAR(20),
    adresse TEXT,
    siret VARCHAR(14),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des devis
CREATE TABLE IF NOT EXISTS devis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    client_id INT NOT NULL,
    numero VARCHAR(50) NOT NULL UNIQUE,
    statut ENUM('brouillon', 'envoyé', 'accepté', 'refusé', 'payé') DEFAULT 'brouillon',
    date_emission DATE NOT NULL,
    date_validite DATE,
    montant_ht DECIMAL(10, 2) DEFAULT 0.00,
    montant_tva DECIMAL(10, 2) DEFAULT 0.00,
    montant_ttc DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des items de devis
CREATE TABLE IF NOT EXISTS devis_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    devis_id INT NOT NULL,
    designation VARCHAR(255) NOT NULL,
    quantite DECIMAL(10, 2) NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    tva DECIMAL(5, 2) NOT NULL,
    total_ht DECIMAL(10, 2) NOT NULL,
    total_ttc DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des factures
CREATE TABLE IF NOT EXISTS factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    client_id INT NOT NULL,
    devis_id INT NULL,
    numero VARCHAR(50) NOT NULL UNIQUE,
    statut ENUM('brouillon', 'envoyée', 'payée', 'annulée', 'retard') DEFAULT 'brouillon',
    date_emission DATE NOT NULL,
    date_echeance DATE NOT NULL,
    montant_ht DECIMAL(10, 2) DEFAULT 0.00,
    montant_tva DECIMAL(10, 2) DEFAULT 0.00,
    montant_ttc DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table des items de facture
CREATE TABLE IF NOT EXISTS facture_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facture_id INT NOT NULL,
    designation VARCHAR(255) NOT NULL,
    quantite DECIMAL(10, 2) NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    tva DECIMAL(5, 2) NOT NULL,
    total_ht DECIMAL(10, 2) NOT NULL,
    total_ttc DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (facture_id) REFERENCES factures(id) ON DELETE CASCADE
) ENGINE=InnoDB;
