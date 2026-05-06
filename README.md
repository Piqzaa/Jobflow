# Jobflow 🚀

Jobflow est une application de gestion complète conçue spécifiquement pour les micro-entrepreneurs et freelances. Elle permet de gérer facilement le cycle de vie des projets, de la prospection à la facturation.

## 🌟 Fonctionnalités

- **Tableau de bord** : Vue d'ensemble de l'activité et statistiques clés.
- **Gestion des Clients** : Centralisation des informations clients et historique.
- **Devis & Factures** : Création, édition et suivi des documents commerciaux.
- **Suivi d'Activité** : Logs des actions importantes pour une meilleure traçabilité.

## 🛠️ Stack Technique

- **Backend** : PHP 8.x (Architecture MVC)
- **Base de données** : MySQL (Données relationnelles) & MongoDB (Logs & Stats)
- **Frontend** : HTML5, SCSS (Méthodologie BEM), JavaScript Vanilla
- **Gestionnaire de dépendances** : Composer

## ⚙️ Installation

1. Cloner le dépôt :
   ```bash
   git clone <url-du-depot>
   ```
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Configurer l'environnement :
   - Dupliquer le fichier `.env.example` en `.env` (si présent).
   - Configurer les accès aux bases de données MySQL et MongoDB.
4. Importer le schéma de la base de données :
   ```bash
   mysql -u root -p jobflow < database/schema.sql
   ```

## 📁 Structure du Projet

- `public/` : Point d'entrée de l'application (assets, index.php).
- `src/` : Code source (Contrôleurs, Modèles, Vues, Services).
- `config/` : Fichiers de configuration.
- `database/` : Scripts SQL pour la base de données.
- `storage/` : Logs et fichiers téléchargés.

---

Développé par Alexandre Berrel
