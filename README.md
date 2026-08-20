# Jobflow 🚀 — CRM pour Micro-Entrepreneurs

**Jobflow** est une solution de gestion complète en **PHP Vanilla (MVC)**, conçue pour automatiser le cycle administratif des freelances. L'application combine la robustesse du relationnel (MySQL) et la flexibilité du NoSQL (MongoDB) pour offrir un outil performant et sécurisé.

---

### 📈 Gestion Commerciale & Ventes

- **Fichier Clients Centralisé** : Gestion complète des clients (SIRET, coordonnées, notes internes).
- **Cycle Devis/Facture** : Création intuitive de devis avec gestion des lignes d'articles, conversion instantanée en facture dès acceptation.
- **Automatisation PDF** : Génération de documents légaux conformes avec calcul automatique des totaux HT/TVA/TTC via **Dompdf**.
- **Statuts Dynamiques** : Suivi du cycle de vie des documents (Brouillon, Envoyé, Payé, En retard).

### 🏦 Pilotage Financier & TVA

- **Tableau de Bord Temps Réel** : Indicateurs clés (CA mensuel/annuel, évolution graphique via **Chart.js**).
- **Gestion Intelligente de la TVA** : Module de calcul automatique de la TVA collectée sur les factures vs la TVA déjà reversée à l'État.
- **Trésorerie** : Visualisation immédiate de la TVA restante à payer.

### 👤 Personnalisation & Identité

- **Profil Entreprise** : Configuration du logo, SIRET, IBAN et BIC pour une personnalisation complète des documents exportés.
- **Espace Sécurisé** : Authentification robuste et gestion du profil utilisateur.

---

## 🛠️ Stack Technique

- **Backend** : PHP 8.1 (Architecture MVC Maison, PSR-4)
- **Bases de données** :
  - **MySQL 8.0** : Données critiques (Users, Clients, Devis, Factures).
  - **MongoDB 7.0** : Logs d'activité (Audit Trail) et cache de statistiques.
- **Frontend** : HTML5, SCSS (**BEM**), JavaScript ES6 Vanilla (Modules).
- **Outils** : Composer, PHPMailer, Mailpit (Testing emails).

---

## 📁 Architecture du Projet

```text
jobflow/
├── public/             # Point d'entrée, assets compilés, uploads logos
├── src/
│   ├── Controllers/    # Orchestration de la logique métier
│   ├── Models/         # Abstraction MySQL (PDO) et MongoDB
│   ├── Views/          # Interface utilisateur (Partials & Templates)
│   ├── Services/       # Services PDF, Email et Logique complexe
│   ├── Helpers/        # Sécurité (CSRF, XSS), Validation, Loggers
│   └── Config/         # Paramétrage DB et constantes
├── database/           # Schémas SQL & Scripts de Seed
├── storage/            # Logs fichiers et dossiers temporaires
└── .env                # Secrets et configuration environnement
```

---

## ⚙️ Installation (Docker)

Le projet est "Plug & Play" grâce à Docker. L'installation des dépendances Composer est automatisée au build.

1. **Cloner le projet**

   ```bash
   git clone https://github.com/Piqzaa/Jobflow
   cd jobflow
   ```

2. **Lancer les services**

   ```bash
   docker-compose up -d --build
   ```

3. **Accéder aux outils**
   - **Application** : `http://localhost:8080`
   - **Mailpit (Intercepteur d'emails)** : `http://localhost:8025`

_Note : La base de données s'auto-initialise au premier lancement via `schema.sql`._

---

## 👤 Compte de Test

Utilisez ces identifiants pour explorer l'interface :

- **Email** : `demo@jobflow.fr`
- **Mot de passe** : `password`

---

## 🛡️ Sécurité & Standards

- **Injections SQL** : Neutralisées via l'utilisation systématique de requêtes préparées (PDO).
- **XSS** : Échappement rigoureux des données via `htmlspecialchars` sur toutes les vues.
- **CSRF** : Protection des formulaires par tokens uniques en session.
- **Hachage** : Utilisation de `password_hash()` avec algorithme BCRYPT.
- **Traçabilité** : Logging des actions sensibles dans MongoDB pour audit.

---

## 🧪 Tests

La suite de tests **PHPUnit** couvre la logique métier et la validation.

```bash
composer install   # installe PHPUnit (une seule fois)
composer test      # lance la suite
```

Couverture actuelle :

- **`Validator`** : email, SIRET, mot de passe, téléphone, champs requis, nombres positifs.
- **`TvaCalculator`** : calcul des montants HT / TVA / TTC (lignes et totaux globaux).
- **`Devis::getNextNumber`** : numérotation annuelle des devis (isolée via des fakes PDO).

> Les calculs TVA ont été extraits dans `src/Services/TvaCalculator.php` pour être testables, ce qui supprime la duplication entre les contrôleurs Devis et Facture.

---

Développé par Alexandre Berrel
