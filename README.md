# 📅 Projet Web – Système de gestion de rendez-vous

## 🎯 Description

Ce projet est une application web permettant aux utilisateurs de prendre rendez-vous pour différents services financiers (assurance, planification, etc.).
Il inclut une gestion complète des utilisateurs, des rendez-vous et un panneau d’administration.

---

## 🚀 Fonctionnalités

### 👤 Utilisateur

* Création de compte (inscription)
* Connexion / déconnexion
* Consultation des services offerts
* Prise de rendez-vous
* Auto-remplissage des informations (nom, email)
* Consultation des rendez-vous personnels
* Annulation de rendez-vous

---

### 🛠️ Administrateur

* Accès sécurisé (rôle admin)
* Visualisation de tous les rendez-vous
* Confirmation / refus des rendez-vous
* Ajout de nouveaux services
* Suppression de services
* Affichage dynamique des statuts (badge visuel)

---

## 🧱 Technologies utilisées

* **PHP** (backend)
* **MySQL / MariaDB** (base de données)
* **PDO** (requêtes sécurisées)
* **Bootstrap 5** (interface utilisateur)
* **HTML / CSS**

---

## 🗄️ Structure de la base de données

### Table `users`

| Champ    | Type                 |
| -------- | -------------------- |
| id       | INT (PK)             |
| nom      | VARCHAR              |
| email    | VARCHAR              |
| password | VARCHAR              |
| role     | VARCHAR (user/admin) |

---

### Table `formations`

| Champ | Type     |
| ----- | -------- |
| id    | INT (PK) |
| titre | VARCHAR  |
| duree | VARCHAR  |
| prix  | FLOAT    |
| actif | BOOLEAN  |

---

### Table `inscriptions`

| Champ        | Type                                   |
| ------------ | -------------------------------------- |
| id           | INT (PK)                               |
| nom          | VARCHAR                                |
| email        | VARCHAR                                |
| date         | DATE                                   |
| heure        | VARCHAR                                |
| formation_id | INT                                    |
| user_id      | INT                                    |
| status       | VARCHAR (en_attente, confirme, refuse) |

---

## 🔐 Sécurité

* Utilisation de `password_hash()` et `password_verify()`
* Protection des routes admin via session
* Validation des données côté serveur
* Requêtes préparées (PDO)

---

## ⚙️ Installation

1. Cloner le projet :

```bash
git clone https://github.com/ton-repo/projet.git
```

2. Importer la base de données dans phpMyAdmin

3. Configurer la connexion DB dans :

```php
src/Database.php
```

4. Lancer le projet via :

```bash
http://localhost/projet
```

---

## 👤 Compte admin (exemple)

* Email : `admin@test.com`
* Mot de passe : `admin123`

---

## ✨ Améliorations UX

* Créneaux désactivés automatiquement si déjà réservés
* Champs utilisateur verrouillés (lecture seule)
* Messages d’erreur clairs
* Interface moderne avec Bootstrap

---

## 📌 État du projet

✔ Migration complète JSON → SQL
✔ CRUD complet (formations + inscriptions)
✔ Authentification fonctionnelle
✔ Interface utilisateur et admin finalisées

---

## 📚 Auteur

Projet réalisé dans le cadre d’un cours de développement web.

---

## 🏁 Conclusion

Ce projet démontre :

* la gestion d’un système complet CRUD
* l’utilisation d’une base de données relationnelle
* l’implémentation d’une authentification sécurisée
* une attention portée à l’expérience utilisateur (UX)

---
