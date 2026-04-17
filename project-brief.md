# 🎲 Aji L3bo Café — Système de Gestion Digitale

> Projet freelance de digitalisation complète de la gestion du café de jeux de société **Aji L3bo Café** (Casablanca).  
> Stack : PHP MVC · Router personnalisé · Namespaces PSR-4 · Composer · PDO/MySQL

---

## 📋 Contexte

Le café Aji L3bo gère actuellement ses réservations sur papier, son inventaire de jeux dans un cahier, et ses sessions de jeu de façon chaotique. L'objectif est de créer un système web complet qui digitalise entièrement ces opérations.

---

## 🎯 Objectifs Techniques

| Contrainte | Détail |
|---|---|
| **Router personnalisé** | Toutes les URLs passent par `index.php` — aucun accès direct aux fichiers PHP |
| **Namespaces PSR-4** | `App\Controllers\`, `App\Models\`, `App\Views\` |
| **Composer** | Autoloading PSR-4 uniquement — zéro `require_once` manuel |
| **MVC strict** | Models = données, Controllers = orchestration, Views = affichage uniquement |

---

## 🗂️ Architecture du Projet

```
aji-l3bo-cafe/
│
├── app/
│   ├── Controllers/
│   │   ├── GameController.php
│   │   ├── ReservationController.php
│   │   ├── SessionController.php
        ├── DashboardController.php
│   │   └── AuthController.php
│   │
│   ├── Models/
│   │   ├── Game.php
│   │   ├── Reservation.php
│   │   ├── Session.php
│   │   ├── Table.php
│   │   └── User.php
│   │
│   └── Views/
│       ├── games/
│       │   ├── index.php
│       │   ├── show.php
│       │   ├── create.php
│       │   └── edit.php
│       ├── reservations/
│       │   ├── index.php
│       │   ├── create.php
│       │   └── my-reservations.php
│       ├── sessions/
│       │   ├── dashboard.php
│       │   ├── create.php
│       │   └── history.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       └── layouts/
│           ├── header.php
│           └── footer.php
│
├── core/
│   ├── Router.php
│   ├── Database.php
│   └── Controller.php
│
├── public/
│   ├── index.php          ← Point d'entrée unique
│   ├── .htaccess
│   ├── css/
│   └── js/
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── composer.json
├── .gitignore
└── README.md
```

---

## 🗺️ Routes du Système

### Module Auth (`/auth`)

| Méthode | URL | Controller::Method | Description |
|---|---|---|---|
| GET | `/login` | `AuthController::loginForm()` | Page de connexion |
| POST | `/login` | `AuthController::login()` | Traitement connexion |
| GET | `/register` | `AuthController::registerForm()` | Page d'inscription |
| POST | `/register` | `AuthController::register()` | Traitement inscription |
| POST | `/logout` | `AuthController::logout()` | Déconnexion |

### Module Jeux (`/games`)

| Méthode | URL | Controller::Method | Description |
|---|---|---|---|
| GET | `/games` | `GameController::index()` | Liste tous les jeux |
| GET | `/games/{id}` | `GameController::show()` | Détail d'un jeu |
| GET | `/games/create` | `GameController::create()` | Formulaire ajout (admin) |
| POST | `/games` | `GameController::store()` | Enregistrer un jeu (admin) |
| GET | `/games/{id}/edit` | `GameController::edit()` | Formulaire modification (admin) |
| POST | `/games/{id}/update` | `GameController::update()` | Mettre à jour un jeu (admin) |
| POST | `/games/{id}/delete` | `GameController::destroy()` | Supprimer un jeu (admin) |

### Module Réservations (`/reservations`)

| Méthode | URL | Controller::Method | Description |
|---|---|---|---|
| GET | `/reservations` | `ReservationController::index()` | Toutes les réservations (admin) |
| GET | `/reservations/create` | `ReservationController::create()` | Formulaire de réservation |
| POST | `/reservations` | `ReservationController::store()` | Créer une réservation |
| GET | `/reservations/my` | `ReservationController::mine()` | Mes réservations (client) |
| POST | `/reservations/{id}/status` | `ReservationController::updateStatus()` | Changer statut (admin) |

### Module Sessions (`/sessions`)

| Méthode | URL | Controller::Method | Description |
|---|---|---|---|
| GET | `/sessions/dashboard` | `SessionController::dashboard()` | Dashboard temps réel (admin) |
| GET | `/sessions/create` | `SessionController::create()` | Démarrer une session |
| POST | `/sessions` | `SessionController::store()` | Enregistrer une session |
| POST | `/sessions/{id}/end` | `SessionController::end()` | Terminer une session |
| GET | `/sessions/history` | `SessionController::history()` | Historique des sessions |

---

## 📄 Pages & Fonctionnalités

### 🔐 Module Auth — Authentification

#### Page : Inscription — `GET /register`
- Champs : Nom complet, Email, Téléphone, Mot de passe, Confirmation mot de passe
- Rôle assigné automatiquement : `client` (les admins sont créés via seed)
- Validation : email unique, mot de passe min 8 caractères, confirmation identique
- Redirection vers `/games` après inscription réussie

#### Page : Connexion — `GET /login`
- Champs : Email, Mot de passe
- Création de la session PHP (`$_SESSION['user_id']`, `$_SESSION['role']`)
- Redirection selon le rôle : admin → `/sessions/dashboard` · client → `/games`
- Message d'erreur si identifiants incorrects

#### Action : Déconnexion — `POST /logout`
- Destruction de la session PHP
- Redirection vers `/login`

---

### 🎲 Module 1 — Catalogue de Jeux

#### Page : Liste des Jeux — `GET /games`
- Affiche tous les jeux disponibles sous forme de cartes
- Chaque carte affiche : nom, catégorie, nombre de joueurs, durée estimée, statut (disponible / en cours)
- **Filtre par catégorie** : Stratégie · Ambiance · Famille · Experts
- Bouton "Voir les détails" par jeu
- Bouton "Ajouter un jeu" visible pour l'admin uniquement

#### Page : Détail d'un Jeu — `GET /games/{id}`
- Affiche toutes les informations du jeu : nom, catégorie, description complète, difficulté, min/max joueurs, durée, statut actuel
- Statut en temps réel : **Disponible** ou **En cours d'utilisation**
- Boutons admin : Modifier / Supprimer

#### Page : Formulaire Ajout/Modification — `GET /games/create` & `GET /games/{id}/edit` *(Admin)*
- Champs : Nom, Catégorie, Description, Difficulté (1–5), Joueurs min/max, Durée estimée, Statut
- Validation côté serveur
- Redirection vers la liste après succès

---

### 📅 Module 2 — Système de Réservations

#### Page : Formulaire de Réservation — `GET /reservations/create`
- **Accès réservé aux utilisateurs connectés** (redirection vers `/login` sinon)
- Champs : Nombre de personnes, Date, Heure de début, **Durée souhaitée** (1h · 2h · 3h · 4h)
- Bouton **"Vérifier la disponibilité"** → requête au serveur avec date + heure + durée
- Affichage dynamique des **tables disponibles** pour ce créneau (aucun chevauchement de réservation confirmée)
- Sélection de la table souhaitée parmi celles disponibles
- Résumé de la réservation avant soumission (table, créneau, durée, nombre de personnes)

> **Logique de disponibilité :** une table est considérée **libre** si aucune réservation `confirmed` ou `pending` sur cette table ne chevauche le créneau `[reserved_at, reserved_at + duration_hours]`.

#### Page : Mes Réservations — `GET /reservations/my`
- **Accès réservé aux utilisateurs connectés** — liste filtrée par `user_id` (session active)
- Affiche : date, heure, durée réservée, table, nombre de personnes, statut
- Statut coloré : **À venir** (vert) · **Complétée** (gris) · **Annulée** (rouge)
- Tri chronologique (plus récentes en haut)

#### Page : Gestion des Réservations *(Admin)* — `GET /reservations`
- Vue complète de toutes les réservations du jour
- Affiche par ligne : client (nom + email), table, date, heure, **durée**, nombre de personnes, statut
- Filtres : par date, par statut
- Actions rapides : Confirmer / Annuler chaque réservation
- Vue planning des tables : visualisation des créneaux occupés sur la journée

---

### 🕹️ Module 3 — Gestion des Sessions

#### Page : Dashboard Sessions Actives *(Admin)* — `GET /sessions/dashboard`
- Vue en temps réel de toutes les tables
- Pour chaque table active : jeu en cours, client, heure de début, **durée réservée**, **temps écoulé**, **temps restant**
- Alerte visuelle si le temps écoulé dépasse la durée de réservation
- Tables libres clairement identifiées
- Bouton "Démarrer une session" sur les tables libres
- Bouton "Terminer la session" sur les tables occupées

#### Page : Démarrer une Session *(Admin)* — `GET /sessions/create`
- Sélectionner une réservation confirmée
- Sélectionner un jeu disponible
- Sélectionner la table associée
- Enregistrement de l'heure de début automatique

#### Page : Terminer une Session *(Admin)* — `POST /sessions/{id}/end`
- Clôture la session : enregistre l'heure de fin
- Libère la table automatiquement
- Met à jour le statut de la réservation à **Complétée**
- Met à jour le statut du jeu à **Disponible**

#### Page : Historique des Sessions *(Admin)* — `GET /sessions/history`
- Tableau complet : client, jeu joué, table, heure début/fin, durée totale
- Filtres : par date, par jeu, par table
- Données exploitables pour les statistiques

---

## 🗄️ Base de Données

### Tables

#### `games`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT PK AUTO_INCREMENT | Identifiant |
| `name` | VARCHAR(100) | Nom du jeu |
| `category` | ENUM | Stratégie · Ambiance · Famille · Experts |
| `description` | TEXT | Description complète |
| `difficulty` | TINYINT(1–5) | Niveau de difficulté |
| `min_players` | TINYINT | Nombre minimum de joueurs |
| `max_players` | TINYINT | Nombre maximum de joueurs |
| `duration_minutes` | INT | Durée estimée en minutes |
| `status` | ENUM | `available` · `in_use` |
| `created_at` | TIMESTAMP | Date d'ajout |

#### `users`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT PK AUTO_INCREMENT | Identifiant |
| `name` | VARCHAR(100) | Nom complet |
| `email` | VARCHAR(150) UNIQUE | Adresse email |
| `phone` | VARCHAR(20) | Numéro de téléphone |
| `password` | VARCHAR(255) | Mot de passe hashé (bcrypt) |
| `role` | ENUM | `client` · `admin` |
| `created_at` | TIMESTAMP | Date d'inscription |

#### `tables`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT PK AUTO_INCREMENT | Identifiant |
| `name` | VARCHAR(50) | Nom/numéro de la table |
| `capacity` | TINYINT | Nombre maximum de personnes |
| `status` | ENUM | `available` · `occupied` |

#### `reservations`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT PK AUTO_INCREMENT | Identifiant |
| `user_id` | INT FK → users | Client ayant réservé |
| `table_id` | INT FK → tables | Table réservée |
| `party_size` | TINYINT | Nombre de personnes |
| `reserved_at` | DATETIME | Date et heure de début souhaitées |
| `duration_hours` | TINYINT | Durée de la réservation en heures (1 · 2 · 3 · 4) |
| `status` | ENUM | `pending` · `confirmed` · `completed` · `cancelled` |
| `created_at` | TIMESTAMP | Date de création |

> **Créneau occupé = `reserved_at` → `reserved_at + INTERVAL duration_hours HOUR`**  
> Utilisé par la requête de disponibilité pour détecter les chevauchements.

#### `sessions`
| Colonne | Type | Description |
|---|---|---|
| `id` | INT PK AUTO_INCREMENT | Identifiant |
| `reservation_id` | INT FK → reservations | Réservation liée |
| `game_id` | INT FK → games | Jeu joué |
| `table_id` | INT FK → tables | Table utilisée |
| `started_at` | DATETIME | Heure de début |
| `ended_at` | DATETIME NULL | Heure de fin (NULL = active) |
| `created_at` | TIMESTAMP | Date de création |

### Relations (Foreign Keys)
```
reservations.user_id        → users.id
reservations.table_id       → tables.id
sessions.reservation_id     → reservations.id
sessions.game_id            → games.id
sessions.table_id           → tables.id
```

### Seed Data (requis)
- **4 tables** (Table 1 à Table 4, capacités variées)
- **15 jeux** (répartis sur les 4 catégories)
- **5 réservations** avec `duration_hours` variées (1h à 4h), statuts variés : `pending`, `confirmed`, `completed`
- **2 sessions actives** (`started_at` défini, `ended_at` NULL)
- **3 users** : 1 `admin` + 2 `client` (mots de passe hashés avec `password_hash()`)

---

## 👤 Rôles Utilisateurs

Les rôles sont gérés via le champ `role` de la table `users`. La session PHP stocke `$_SESSION['user_id']` et `$_SESSION['role']` à la connexion.

### Client (`role = 'client'`)
- Accès après connexion (inscription libre)
- Parcourir et filtrer les jeux
- Voir le détail d'un jeu
- Créer une réservation (avec durée)
- Consulter ses propres réservations

### Admin (`role = 'admin'`)
- Tout ce que peut faire le client
- Ajouter / modifier / supprimer des jeux
- Voir et gérer toutes les réservations (confirmer / annuler)
- Démarrer / terminer des sessions
- Accéder au dashboard temps réel (avec alerte dépassement de durée)
- Consulter l'historique complet des sessions

---

## ⭐ Bonus Choisi *(à définir en trinôme)*

Choisir UNE option parmi :

| Option | Description |
|---|---|
| **Statistiques Admin** | Dashboard : jeux les plus joués, heures de pointe, taux d'occupation |
| **Système de Notation** | Les clients notent les jeux après session (1–5 étoiles) |
| **Recommandations** | Suggérer des jeux selon le nombre de joueurs de la réservation |
| **Timer Visuel** | Compte à rebours graphique sur le dashboard des sessions actives |

---

## 📦 Livrables Attendus

### 1. Repository GitHub
- Minimum **20 commits** répartis entre les 3 membres
- Messages de commit explicites (`Add Game CRUD routes`, `Implement Reservation validation`…)
- Feature branches visibles dans l'historique
- Pull Requests avec au minimum **1 commentaire de review** par PR

### 2. Fichier SQL (`database/schema.sql` + `database/seed.sql`)
- Création complète des tables avec Foreign Keys
- Données de seed conformes aux spécifications

### 3. README.md
- Description du projet
- Screenshot du board Jira final
- Arborescence de l'architecture
- Instructions d'installation (`composer install`, import SQL)
- Table des routes disponibles

### 4. Fichiers Composer
- `composer.json` configuré avec PSR-4
- `vendor/` dans `.gitignore`

---

## ✅ Critères d'Évaluation

| Critère | Poids | Points clés |
|---|---|---|
| **Architecture** | 40% | Router fonctionnel, PSR-4, autoloading, MVC strict |
| **Collaboration** | 30% | Modules répartis, standups, Jira, PRs reviewées, commits équilibrés |
| **Code Quality** | 20% | FK + JOINs, PDO prepared statements, validation, gestion 404 |
| **Process Agile** | 10% | Jira livré lundi 16h, rétrospective mid-week, rétrospective finale |

---

## 🗓️ Planning

| Date | Événement |
|---|---|
| Lundi 13/04/2026 – 10:00 | Lancement du projet |
| Lundi 13/04/2026 – 16:00 | Jira board à livrer |
| Milieu de semaine | Rétrospective mid-week |
| Vendredi 17/04/2026 – 16:00 | **Deadline livraison** |
| Entretien trinôme | Démo live · Code review · Live coding · Questions process |

### Déroulé de l'Entretien (45 min)
1. **Démonstration live** (15 min) — Parcours complet utilisateur + présentation Jira + architecture
2. **Code review collective** (15 min) — Membre 1 : Router · Membre 2 : SQL/JOINs · Membre 3 : Autoloading
3. **Live coding** (10 min) — Modification en direct imposée par l'évaluateur
4. **Questions process** (5 min) — Répartition du travail, blockers, exemple de code review

---

*Projet réalisé en trinôme — DigitalBite Agency · Avril 2026*