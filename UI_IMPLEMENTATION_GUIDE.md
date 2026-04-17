# 🎲 Aji L3bo Café — UI Implementation Guide for AI Coding Agent

**Project:** Aji L3bo Café — Système de Gestion Digitale  
**Purpose:** Complete UI implementation in `views/` folder with separate admin/user sections  
**Stack:** HTML5, CSS3, vanilla JavaScript  
**Status:** Ready for automated implementation  

---

## 📋 Table of Contents

1. [Project Structure](#project-structure)
2. [CSS Organization](#css-organization)
3. [File-by-File Implementation](#file-by-file-implementation)
4. [Admin Pages](#admin-pages)
5. [User Pages](#user-pages)
6. [Shared Components](#shared-components)
7. [CSS Variables & Design System](#css-variables--design-system)
8. [Critical Implementation Notes](#critical-implementation-notes)

---

## 📂 Project Structure

```
app/
├── Views/
│   ├── layouts/
│   │   ├── header.php          ← SHARED - renders different nav based on role
│   │   ├── footer.php          ← SHARED
│   │   ├── admin-sidebar.php   ← ADMIN ONLY - left navigation
│   │   └── user-navbar.php     ← USER ONLY - top navigation
│   │
│   ├── auth/
│   │   ├── login.php           ← PUBLIC
│   │   └── register.php        ← PUBLIC
│   │
│   ├── games/
│   │   ├── index.php           ← USER VIEW (browseable)
│   │   ├── show.php            ← USER VIEW (game details)
│   │   ├── create.php          ← ADMIN ONLY (add game form)
│   │   └── edit.php            ← ADMIN ONLY (edit game form)
│   │
│   ├── reservations/
│   │   ├── index.php           ← ADMIN ONLY (all reservations + management)
│   │   ├── create.php          ← USER VIEW (reservation form with availability)
│   │   └── my-reservations.php ← USER VIEW (user's bookings)
│   │
│   └── sessions/
│       ├── dashboard.php       ← ADMIN ONLY (real-time table dashboard)
│       ├── create.php          ← ADMIN ONLY (start session form)
│       └── history.php         ← ADMIN ONLY (session history)
│
public/
├── css/
│   ├── main.css                ← Global styles + design system
│   ├── auth.css                ← Login/register page styles
│   ├── games.css               ← Game catalog + detail styles
│   ├── reservations.css        ← Reservation pages styles
│   ├── sessions.css            ← Session dashboard + history styles
│   └── responsive.css          ← Mobile/tablet breakpoints
│
├── js/
│   ├── main.js                 ← Global utilities
│   ├── reservations.js         ← Availability checker + dynamic table selection
│   └── sessions.js             ← Real-time dashboard updates
│
└── index.php                    ← Router entry point
```

---

## 🎨 CSS Organization

### CSS File Strategy

Each CSS file is **modular and self-contained**:
- `main.css` → CSS variables, global styles, layout foundations, design system
- `auth.css` → Forms, card layouts for login/register
- `games.css` → Card grid, filters, detail page layouts
- `reservations.css` → Availability table, form components, status badges
- `sessions.css` → Dashboard grid, real-time updates, timer styling
- `responsive.css` → Mobile-first media queries for all components

### Loading Order in Layout

```html
<head>
    <!-- Critical path -->
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/auth.css">
    <link rel="stylesheet" href="/public/css/games.css">
    <link rel="stylesheet" href="/public/css/reservations.css">
    <link rel="stylesheet" href="/public/css/sessions.css">
    <link rel="stylesheet" href="/public/css/responsive.css">
</head>
```

---

## 🎯 CSS Variables & Design System

Create in `public/css/main.css` at the top:

```css
:root {
    /* === COLOR PALETTE === */
    --color-primary: #1a472a;      /* Deep forest green */
    --color-primary-light: #2d6a3f;
    --color-primary-dark: #0f2d1a;
    
    --color-accent: #d4823f;       /* Terracotta/orange */
    --color-accent-light: #e8a860;
    --color-accent-dark: #a85f2f;
    
    --color-success: #27ae60;      /* Green for "available" */
    --color-warning: #f39c12;      /* Orange for "pending" */
    --color-danger: #e74c3c;       /* Red for "cancelled" */
    --color-info: #3498db;         /* Blue for "in progress" */
    
    --color-light: #f5f5f5;        /* Very light gray */
    --color-lighter: #ffffff;      /* White */
    --color-dark: #2c3e50;         /* Dark blue-gray */
    --color-darker: #1a1a1a;       /* Nearly black */
    
    --color-border: #e0e0e0;
    --color-shadow: rgba(0, 0, 0, 0.1);
    
    /* === SPACING === */
    --spacing-xs: 0.5rem;
    --spacing-sm: 1rem;
    --spacing-md: 1.5rem;
    --spacing-lg: 2rem;
    --spacing-xl: 3rem;
    --spacing-xxl: 4rem;
    
    /* === TYPOGRAPHY === */
    --font-family-base: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    --font-family-heading: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.5rem;
    --font-size-xxl: 2rem;
    
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-semibold: 600;
    --font-weight-bold: 700;
    
    --line-height-tight: 1.2;
    --line-height-normal: 1.5;
    --line-height-relaxed: 1.75;
    
    /* === SHADOWS === */
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);
    
    /* === BORDERS === */
    --border-radius-sm: 4px;
    --border-radius-md: 8px;
    --border-radius-lg: 12px;
    
    /* === TRANSITIONS === */
    --transition-fast: 150ms ease;
    --transition-base: 250ms ease;
    --transition-slow: 350ms ease;
}
```

---

## 📝 File-by-File Implementation

### ✅ Implementation Checklist

Each file must include:
- [ ] Semantic HTML5 structure
- [ ] CSS variables for all colors/spacing
- [ ] Mobile-first responsive design
- [ ] Accessibility attributes (aria-*, role, alt text)
- [ ] Clear class naming (BEM or utility classes)
- [ ] Form validation attributes
- [ ] Error state styling
- [ ] Success state styling
- [ ] Loading states where applicable

---

## 🔐 AUTH PAGES

### 1. `app/Views/auth/login.php`

**Purpose:** Public login page  
**Route:** `GET /login`  
**Accessible by:** Everyone (redirects to `/games` if already logged in)

**HTML Structure:**
```html
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>🎲 Aji L3bo Café</h1>
            <p>Connexion</p>
        </div>
        
        <form method="POST" action="/login" class="auth-form" id="loginForm">
            <!-- Error message (if exists) -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Email field -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control"
                    required
                    placeholder="vous@exemple.com"
                    aria-label="Adresse email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
                <span class="form-error" id="emailError"></span>
            </div>
            
            <!-- Password field -->
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control"
                    required
                    placeholder="••••••••"
                    aria-label="Mot de passe"
                >
                <span class="form-error" id="passwordError"></span>
            </div>
            
            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-block">
                Se connecter
            </button>
        </form>
        
        <!-- Register link -->
        <div class="auth-footer">
            <p>Pas encore inscrit? <a href="/register" class="link">Créer un compte</a></p>
        </div>
    </div>
</div>
```

**CSS Requirements (in `auth.css`):**
```css
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    padding: var(--spacing-md);
}

.auth-card {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-xl);
    padding: var(--spacing-xl);
    width: 100%;
    max-width: 400px;
}

.auth-header {
    text-align: center;
    margin-bottom: var(--spacing-lg);
}

.auth-header h1 {
    font-size: var(--font-size-xxl);
    color: var(--color-primary);
    margin: 0 0 var(--spacing-xs) 0;
}

.auth-header p {
    font-size: var(--font-size-lg);
    color: var(--color-dark);
    margin: 0;
    font-weight: var(--font-weight-medium);
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
    margin-bottom: var(--spacing-xs);
}

.form-control {
    padding: var(--spacing-sm);
    border: 2px solid var(--color-border);
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-base);
    font-family: var(--font-family-base);
    transition: border-color var(--transition-base);
}

.form-control:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(26, 71, 42, 0.1);
}

.form-control:invalid {
    border-color: var(--color-danger);
}

.form-error {
    font-size: var(--font-size-xs);
    color: var(--color-danger);
    margin-top: var(--spacing-xs);
    display: none;
}

.form-error.show {
    display: block;
}

.btn {
    padding: var(--spacing-sm) var(--spacing-md);
    border: none;
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    cursor: pointer;
    transition: all var(--transition-base);
}

.btn-primary {
    background: var(--color-primary);
    color: var(--color-lighter);
}

.btn-primary:hover {
    background: var(--color-primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-block {
    width: 100%;
}

.alert {
    padding: var(--spacing-md);
    border-radius: var(--border-radius-md);
    margin-bottom: var(--spacing-md);
    font-weight: var(--font-weight-medium);
}

.alert-danger {
    background: rgba(231, 76, 60, 0.1);
    color: var(--color-danger);
    border: 1px solid var(--color-danger);
}

.auth-footer {
    text-align: center;
    margin-top: var(--spacing-lg);
    font-size: var(--font-size-sm);
}

.link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: var(--font-weight-semibold);
}

.link:hover {
    text-decoration: underline;
}
```

---

### 2. `app/Views/auth/register.php`

**Purpose:** Public registration page  
**Route:** `GET /register`  
**Accessible by:** Everyone (auto-assigns role = "client")

**HTML Structure:**
```html
<div class="auth-container">
    <div class="auth-card auth-card-wide">
        <div class="auth-header">
            <h1>🎲 Aji L3bo Café</h1>
            <p>Créer un compte</p>
        </div>
        
        <form method="POST" action="/register" class="auth-form" id="registerForm">
            <!-- Error message (if exists) -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Name field -->
            <div class="form-group">
                <label for="name" class="form-label">Nom complet *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control"
                    required
                    minlength="3"
                    placeholder="Jean Dupont"
                    aria-label="Nom complet"
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                >
                <span class="form-error" id="nameError"></span>
            </div>
            
            <!-- Email field -->
            <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control"
                    required
                    placeholder="jean@exemple.com"
                    aria-label="Adresse email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
                <span class="form-error" id="emailError"></span>
            </div>
            
            <!-- Phone field -->
            <div class="form-group">
                <label for="phone" class="form-label">Téléphone *</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form-control"
                    required
                    placeholder="+212 6XX XXX XXX"
                    aria-label="Numéro de téléphone"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                >
                <span class="form-error" id="phoneError"></span>
            </div>
            
            <!-- Password field -->
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe (min 8 caractères) *</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control"
                    required
                    minlength="8"
                    placeholder="••••••••"
                    aria-label="Mot de passe"
                >
                <span class="form-error" id="passwordError"></span>
            </div>
            
            <!-- Password confirm field -->
            <div class="form-group">
                <label for="password_confirm" class="form-label">Confirmer le mot de passe *</label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    class="form-control"
                    required
                    minlength="8"
                    placeholder="••••••••"
                    aria-label="Confirmation du mot de passe"
                >
                <span class="form-error" id="password_confirmError"></span>
            </div>
            
            <!-- Note about role -->
            <div class="note">
                💡 Vous serez inscrit en tant que <strong>client</strong>. Les administrateurs sont créés séparément.
            </div>
            
            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-block">
                Créer mon compte
            </button>
        </form>
        
        <!-- Login link -->
        <div class="auth-footer">
            <p>Déjà inscrit? <a href="/login" class="link">Se connecter</a></p>
        </div>
    </div>
</div>
```

**CSS Additions (in `auth.css`):**
```css
.auth-card-wide {
    max-width: 500px;
}

.note {
    background: rgba(52, 152, 219, 0.1);
    border-left: 4px solid var(--color-info);
    padding: var(--spacing-md);
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-sm);
    color: var(--color-dark);
    margin-bottom: var(--spacing-md);
}
```

---

## 🎮 GAMES PAGES

### 3. `app/Views/games/index.php`

**Purpose:** Game catalog browsable by all users  
**Route:** `GET /games`  
**Features:** Card grid, category filter, search, admin "Add Game" button  
**User-facing:** ✅ CLIENT CAN VIEW | ❌ ADMIN ONLY CONTROLS

**HTML Structure:**
```html
<div class="games-container">
    <!-- Header section -->
    <div class="games-header">
        <h1>🎲 Nos Jeux</h1>
        <p class="subtitle">Découvrez notre collection de jeux de société</p>
    </div>
    
    <!-- Toolbar: filters + admin button -->
    <div class="games-toolbar">
        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="categoryFilter" class="filter-label">Catégorie:</label>
                <select id="categoryFilter" name="category" class="filter-select">
                    <option value="">Toutes les catégories</option>
                    <option value="strategie">Stratégie</option>
                    <option value="ambiance">Ambiance</option>
                    <option value="famille">Famille</option>
                    <option value="experts">Experts</option>
                </select>
            </div>
            
            <div class="filter-group">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="filter-search"
                    placeholder="Rechercher un jeu..."
                    aria-label="Rechercher un jeu"
                >
            </div>
        </div>
        
        <!-- Admin button (visible only for admin) -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="/games/create" class="btn btn-accent" aria-label="Ajouter un nouveau jeu">
                ➕ Ajouter un jeu
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Games grid -->
    <div class="games-grid">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card" data-category="<?= htmlspecialchars($game['category']) ?>" data-name="<?= htmlspecialchars($game['name']) ?>">
                    <!-- Card header with status badge -->
                    <div class="game-card-header">
                        <span class="status-badge status-<?= htmlspecialchars($game['status']) ?>">
                            <?= $game['status'] === 'available' ? '✓ Disponible' : '⏳ En cours' ?>
                        </span>
                    </div>
                    
                    <!-- Card body -->
                    <div class="game-card-body">
                        <h3 class="game-card-title"><?= htmlspecialchars($game['name']) ?></h3>
                        
                        <div class="game-meta">
                            <div class="meta-item">
                                <span class="meta-label">Catégorie:</span>
                                <span class="meta-value"><?= htmlspecialchars($game['category']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Joueurs:</span>
                                <span class="meta-value"><?= htmlspecialchars($game['min_players']) ?>-<?= htmlspecialchars($game['max_players']) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Durée:</span>
                                <span class="meta-value"><?= htmlspecialchars($game['duration_minutes']) ?> min</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Difficulté:</span>
                                <span class="meta-stars">
                                    <?php for ($i = 0; $i < $game['difficulty']; $i++): ?>
                                        ⭐
                                    <?php endfor; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card footer with actions -->
                    <div class="game-card-footer">
                        <a href="/games/<?= htmlspecialchars($game['id']) ?>" class="btn btn-sm btn-primary">
                            Voir les détails
                        </a>
                        
                        <!-- Admin controls (visible only for admin) -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <div class="admin-actions">
                                <a href="/games/<?= htmlspecialchars($game['id']) ?>/edit" class="btn btn-sm btn-secondary" aria-label="Modifier ce jeu">
                                    ✏️
                                </a>
                                <form method="POST" action="/games/<?= htmlspecialchars($game['id']) ?>/delete" class="admin-delete-form" style="display: inline;">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')" aria-label="Supprimer ce jeu">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucun jeu trouvé</p>
            </div>
        <?php endif; ?>
    </div>
</div>
```

**CSS Requirements (in `games.css`):**
```css
.games-container {
    padding: var(--spacing-lg);
    max-width: 1400px;
    margin: 0 auto;
}

.games-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.games-header h1 {
    font-size: var(--font-size-xxl);
    color: var(--color-primary);
    margin: 0 0 var(--spacing-xs) 0;
}

.subtitle {
    font-size: var(--font-size-lg);
    color: var(--color-dark);
    margin: 0;
}

.games-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: var(--spacing-lg);
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.filters {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.filter-label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
}

.filter-select,
.filter-search {
    padding: var(--spacing-sm);
    border: 2px solid var(--color-border);
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-base);
    font-family: var(--font-family-base);
    min-width: 150px;
}

.filter-search {
    min-width: 200px;
}

.filter-select:focus,
.filter-search:focus {
    outline: none;
    border-color: var(--color-primary);
}

.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--spacing-lg);
    margin-top: var(--spacing-lg);
}

.game-card {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all var(--transition-base);
    display: flex;
    flex-direction: column;
}

.game-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.game-card-header {
    background: var(--color-primary-light);
    padding: var(--spacing-md);
    display: flex;
    justify-content: flex-end;
}

.status-badge {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--border-radius-sm);
}

.status-available {
    background: var(--color-success);
    color: var(--color-lighter);
}

.status-in_use {
    background: var(--color-warning);
    color: var(--color-lighter);
}

.game-card-body {
    padding: var(--spacing-md);
    flex-grow: 1;
}

.game-card-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
    margin: 0 0 var(--spacing-md) 0;
}

.game-meta {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.meta-item {
    display: flex;
    justify-content: space-between;
    font-size: var(--font-size-sm);
}

.meta-label {
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
}

.meta-value {
    color: var(--color-dark);
}

.meta-stars {
    font-size: var(--font-size-sm);
}

.game-card-footer {
    padding: var(--spacing-md);
    border-top: 1px solid var(--color-border);
    display: flex;
    gap: var(--spacing-sm);
    justify-content: space-between;
    align-items: center;
}

.btn-sm {
    padding: var(--spacing-xs) var(--spacing-sm);
    font-size: var(--font-size-sm);
}

.admin-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--spacing-xxl);
    color: var(--color-dark);
    font-size: var(--font-size-lg);
}
```

---

### 4. `app/Views/games/show.php`

**Purpose:** Detailed game view  
**Route:** `GET /games/{id}`  
**Features:** Full details, edit/delete buttons for admin

**HTML Structure:**
```html
<div class="game-detail-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/games" class="breadcrumb-link">← Retour aux jeux</a>
    </div>
    
    <!-- Main content -->
    <div class="game-detail-wrapper">
        <!-- Left: Game info -->
        <div class="game-detail-main">
            <div class="game-detail-header">
                <h1><?= htmlspecialchars($game['name']) ?></h1>
                <span class="status-badge status-<?= htmlspecialchars($game['status']) ?>" style="margin-left: var(--spacing-sm);">
                    <?= $game['status'] === 'available' ? '✓ Disponible' : '⏳ En cours' ?>
                </span>
            </div>
            
            <div class="game-detail-body">
                <!-- Description -->
                <section class="detail-section">
                    <h2>Description</h2>
                    <p><?= htmlspecialchars($game['description']) ?></p>
                </section>
                
                <!-- Specs grid -->
                <section class="detail-section">
                    <h2>Caractéristiques</h2>
                    <div class="specs-grid">
                        <div class="spec-item">
                            <span class="spec-label">Catégorie</span>
                            <span class="spec-value"><?= htmlspecialchars($game['category']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Difficulté</span>
                            <span class="spec-value">
                                <?php for ($i = 0; $i < $game['difficulty']; $i++): ?>
                                    ⭐
                                <?php endfor; ?>
                            </span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Nombre de joueurs</span>
                            <span class="spec-value"><?= htmlspecialchars($game['min_players']) ?>-<?= htmlspecialchars($game['max_players']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Durée estimée</span>
                            <span class="spec-value"><?= htmlspecialchars($game['duration_minutes']) ?> minutes</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        
        <!-- Right sidebar: Actions -->
        <aside class="game-detail-sidebar">
            <div class="action-card">
                <!-- Admin controls -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div class="admin-panel">
                        <h3>Gestion Admin</h3>
                        <a href="/games/<?= htmlspecialchars($game['id']) ?>/edit" class="btn btn-secondary btn-block">
                            ✏️ Modifier
                        </a>
                        <form method="POST" action="/games/<?= htmlspecialchars($game['id']) ?>/delete" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce jeu?');">
                            <button type="submit" class="btn btn-danger btn-block">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- User action -->
                    <p class="note">✓ Ce jeu est disponible pour la réservation</p>
                    <a href="/reservations/create" class="btn btn-primary btn-block">
                        🎯 Réserver ce jeu
                    </a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
```

**CSS Additions (in `games.css`):**
```css
.game-detail-container {
    padding: var(--spacing-lg);
    max-width: 1200px;
    margin: 0 auto;
}

.breadcrumb {
    margin-bottom: var(--spacing-lg);
}

.breadcrumb-link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: var(--font-weight-semibold);
}

.breadcrumb-link:hover {
    text-decoration: underline;
}

.game-detail-wrapper {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--spacing-xl);
}

.game-detail-main {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.game-detail-header {
    display: flex;
    align-items: center;
    margin-bottom: var(--spacing-lg);
}

.game-detail-header h1 {
    font-size: var(--font-size-xxl);
    color: var(--color-primary);
    margin: 0;
}

.game-detail-body {
    color: var(--color-dark);
}

.detail-section {
    margin-bottom: var(--spacing-lg);
}

.detail-section h2 {
    font-size: var(--font-size-lg);
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--color-primary-light);
    padding-bottom: var(--spacing-sm);
}

.detail-section p {
    line-height: var(--line-height-relaxed);
    color: var(--color-dark);
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
}

.spec-item {
    background: var(--color-light);
    padding: var(--spacing-md);
    border-radius: var(--border-radius-md);
    border-left: 4px solid var(--color-primary);
}

.spec-label {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
    margin-bottom: var(--spacing-xs);
}

.spec-value {
    display: block;
    font-size: var(--font-size-base);
    color: var(--color-primary);
    font-weight: var(--font-weight-bold);
}

.game-detail-sidebar {
    display: flex;
    flex-direction: column;
}

.action-card {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
    position: sticky;
    top: var(--spacing-lg);
}

.admin-panel h3 {
    margin: 0 0 var(--spacing-md) 0;
    color: var(--color-primary);
    font-size: var(--font-size-base);
}

.admin-panel .btn {
    margin-bottom: var(--spacing-sm);
}

.admin-panel .btn:last-child {
    margin-bottom: 0;
}
```

---

### 5. `app/Views/games/create.php` & `edit.php`

**Purpose:** Game creation/editing form (ADMIN ONLY)  
**Routes:** `GET /games/create`, `GET /games/{id}/edit`  
**Access:** Admin only - implement permission check in controller

**HTML Structure (same for both, adapt form action):**
```html
<div class="form-container">
    <div class="form-header">
        <h1><?= isset($game) ? '✏️ Modifier le jeu' : '➕ Ajouter un nouveau jeu' ?></h1>
    </div>
    
    <form method="POST" action="<?= isset($game) ? '/games/' . htmlspecialchars($game['id']) . '/update' : '/games' ?>" class="game-form">
        <!-- Error messages -->
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Nom du jeu *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-control"
                required
                minlength="3"
                maxlength="100"
                value="<?= htmlspecialchars($game['name'] ?? '') ?>"
                placeholder="Catan"
            >
        </div>
        
        <!-- Category -->
        <div class="form-group">
            <label for="category" class="form-label">Catégorie *</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="strategie" <?= (isset($game) && $game['category'] === 'strategie') ? 'selected' : '' ?>>Stratégie</option>
                <option value="ambiance" <?= (isset($game) && $game['category'] === 'ambiance') ? 'selected' : '' ?>>Ambiance</option>
                <option value="famille" <?= (isset($game) && $game['category'] === 'famille') ? 'selected' : '' ?>>Famille</option>
                <option value="experts" <?= (isset($game) && $game['category'] === 'experts') ? 'selected' : '' ?>>Experts</option>
            </select>
        </div>
        
        <!-- Description -->
        <div class="form-group">
            <label for="description" class="form-label">Description *</label>
            <textarea 
                id="description" 
                name="description" 
                class="form-control form-textarea"
                required
                minlength="10"
                maxlength="1000"
                rows="4"
                placeholder="Décrivez le jeu..."
            ><?= htmlspecialchars($game['description'] ?? '') ?></textarea>
        </div>
        
        <!-- Difficulty -->
        <div class="form-group">
            <label for="difficulty" class="form-label">Difficulté (1-5) *</label>
            <input 
                type="number" 
                id="difficulty" 
                name="difficulty" 
                class="form-control"
                required
                min="1"
                max="5"
                value="<?= htmlspecialchars($game['difficulty'] ?? '') ?>"
            >
        </div>
        
        <!-- Min players -->
        <div class="form-group">
            <label for="min_players" class="form-label">Nombre minimum de joueurs *</label>
            <input 
                type="number" 
                id="min_players" 
                name="min_players" 
                class="form-control"
                required
                min="1"
                value="<?= htmlspecialchars($game['min_players'] ?? '') ?>"
            >
        </div>
        
        <!-- Max players -->
        <div class="form-group">
            <label for="max_players" class="form-label">Nombre maximum de joueurs *</label>
            <input 
                type="number" 
                id="max_players" 
                name="max_players" 
                class="form-control"
                required
                min="1"
                value="<?= htmlspecialchars($game['max_players'] ?? '') ?>"
            >
        </div>
        
        <!-- Duration -->
        <div class="form-group">
            <label for="duration_minutes" class="form-label">Durée estimée (minutes) *</label>
            <input 
                type="number" 
                id="duration_minutes" 
                name="duration_minutes" 
                class="form-control"
                required
                min="5"
                step="5"
                value="<?= htmlspecialchars($game['duration_minutes'] ?? '') ?>"
            >
        </div>
        
        <!-- Status (edit only) -->
        <?php if (isset($game)): ?>
            <div class="form-group">
                <label for="status" class="form-label">Statut *</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="available" <?= $game['status'] === 'available' ? 'selected' : '' ?>>Disponible</option>
                    <option value="in_use" <?= $game['status'] === 'in_use' ? 'selected' : '' ?>>En cours d'utilisation</option>
                </select>
            </div>
        <?php endif; ?>
        
        <!-- Submit buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= isset($game) ? 'Mettre à jour' : 'Créer le jeu' ?>
            </button>
            <a href="/games" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
```

**CSS Additions (in `games.css`):**
```css
.form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: var(--spacing-lg);
}

.form-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.form-header h1 {
    font-size: var(--font-size-xxl);
    color: var(--color-primary);
    margin: 0;
}

.game-form {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.form-textarea {
    resize: vertical;
    padding: var(--spacing-sm);
    font-family: var(--font-family-base);
}

.form-actions {
    display: flex;
    gap: var(--spacing-md);
    margin-top: var(--spacing-lg);
}

.form-actions .btn {
    flex: 1;
}

.btn-secondary {
    background: var(--color-light);
    color: var(--color-dark);
    border: 2px solid var(--color-border);
}

.btn-secondary:hover {
    background: var(--color-border);
}

.btn-danger {
    background: var(--color-danger);
    color: var(--color-lighter);
}

.btn-danger:hover {
    background: #c0392b;
}
```

---

## 📅 RESERVATIONS PAGES

### 6. `app/Views/reservations/create.php`

**Purpose:** Reservation form with dynamic availability checking (USER)  
**Route:** `GET /reservations/create`  
**Features:** Date/time picker, availability checker, table selector, summary

**Key Implementation Notes:**
- **Requires authentication** - Redirect to `/login` if `!$_SESSION['user_id']`
- **Dynamic availability** - JS sends AJAX to check available tables
- **Duration-based slots** - Options: 1h, 2h, 3h, 4h

**HTML Structure:**
```html
<div class="reservation-container">
    <h1>🎯 Nouvelle Réservation</h1>
    
    <form method="POST" action="/reservations" class="reservation-form" id="reservationForm">
        <!-- Part 1: Reservation details -->
        <fieldset class="form-section">
            <legend>Détails de votre réservation</legend>
            
            <!-- Party size -->
            <div class="form-group">
                <label for="party_size" class="form-label">Nombre de personnes *</label>
                <input 
                    type="number" 
                    id="party_size" 
                    name="party_size" 
                    class="form-control"
                    required
                    min="1"
                    max="10"
                    value="<?= htmlspecialchars($_POST['party_size'] ?? '') ?>"
                    placeholder="2"
                >
            </div>
            
            <!-- Date -->
            <div class="form-group">
                <label for="reserved_at" class="form-label">Date souhaitée *</label>
                <input 
                    type="date" 
                    id="reserved_at" 
                    name="date"
                    class="form-control"
                    required
                    min="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars($_POST['date'] ?? '') ?>"
                >
            </div>
            
            <!-- Time -->
            <div class="form-group">
                <label for="time" class="form-label">Heure de début *</label>
                <input 
                    type="time" 
                    id="time" 
                    name="time"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($_POST['time'] ?? '') ?>"
                >
            </div>
            
            <!-- Duration -->
            <div class="form-group">
                <label for="duration_hours" class="form-label">Durée souhaitée *</label>
                <div class="duration-selector">
                    <label class="duration-option">
                        <input type="radio" name="duration_hours" value="1" <?= (isset($_POST['duration_hours']) && $_POST['duration_hours'] === '1') ? 'checked' : '' ?>>
                        <span>1 heure</span>
                    </label>
                    <label class="duration-option">
                        <input type="radio" name="duration_hours" value="2" <?= (isset($_POST['duration_hours']) && $_POST['duration_hours'] === '2') ? 'checked' : '' ?>>
                        <span>2 heures</span>
                    </label>
                    <label class="duration-option">
                        <input type="radio" name="duration_hours" value="3" <?= (isset($_POST['duration_hours']) && $_POST['duration_hours'] === '3') ? 'checked' : '' ?>>
                        <span>3 heures</span>
                    </label>
                    <label class="duration-option">
                        <input type="radio" name="duration_hours" value="4" <?= (isset($_POST['duration_hours']) && $_POST['duration_hours'] === '4') ? 'checked' : '' ?>>
                        <span>4 heures</span>
                    </label>
                </div>
            </div>
        </fieldset>
        
        <!-- Part 2: Availability check button -->
        <div class="availability-section">
            <button type="button" id="checkAvailabilityBtn" class="btn btn-info">
                🔍 Vérifier la disponibilité
            </button>
            <div id="availabilityLoader" class="loader" style="display: none;">
                ⏳ Vérification...
            </div>
        </div>
        
        <!-- Part 3: Available tables -->
        <fieldset class="form-section" id="tablesSection" style="display: none;">
            <legend>Tables disponibles</legend>
            <div id="availableTables" class="tables-grid">
                <!-- Tables populated by JS -->
            </div>
        </fieldset>
        
        <!-- Part 4: Reservation summary -->
        <div class="summary-section" id="summarySection" style="display: none;">
            <h3>📋 Résumé de votre réservation</h3>
            <div class="summary-item">
                <span>Nombre de personnes:</span>
                <strong id="summaryPartySize">-</strong>
            </div>
            <div class="summary-item">
                <span>Date et heure:</span>
                <strong id="summaryDateTime">-</strong>
            </div>
            <div class="summary-item">
                <span>Durée:</span>
                <strong id="summaryDuration">-</strong>
            </div>
            <div class="summary-item">
                <span>Table sélectionnée:</span>
                <strong id="summaryTable">-</strong>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block" id="submitBtn" disabled>
                ✓ Confirmer la réservation
            </button>
        </div>
    </form>
</div>

<script>
// Requires reservations.js
</script>
```

**CSS Additions (in `reservations.css`):**
```css
.reservation-container {
    max-width: 700px;
    margin: 0 auto;
    padding: var(--spacing-lg);
}

.reservation-container h1 {
    text-align: center;
    color: var(--color-primary);
    margin-bottom: var(--spacing-xl);
}

.reservation-form {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.form-section {
    border: none;
    margin-bottom: var(--spacing-lg);
    padding: 0;
}

.form-section legend {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
    padding: 0;
}

.duration-selector {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
}

.duration-option {
    display: flex;
    align-items: center;
    padding: var(--spacing-md);
    background: var(--color-light);
    border-radius: var(--border-radius-md);
    cursor: pointer;
    border: 2px solid transparent;
    transition: all var(--transition-base);
}

.duration-option:hover {
    background: var(--color-border);
}

.duration-option input[type="radio"] {
    margin-right: var(--spacing-sm);
    cursor: pointer;
}

.duration-option input[type="radio"]:checked + span {
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}

.availability-section {
    text-align: center;
    margin: var(--spacing-lg) 0;
}

.btn-info {
    background: var(--color-info);
    color: var(--color-lighter);
}

.btn-info:hover {
    background: #2980b9;
}

.loader {
    font-size: var(--font-size-lg);
    color: var(--color-info);
    margin-top: var(--spacing-md);
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
}

.table-option {
    padding: var(--spacing-md);
    background: var(--color-light);
    border-radius: var(--border-radius-md);
    cursor: pointer;
    border: 2px solid var(--color-border);
    transition: all var(--transition-base);
    text-align: center;
}

.table-option:hover {
    border-color: var(--color-primary);
    background: rgba(26, 71, 42, 0.05);
}

.table-option input[type="radio"]:checked {
    accent-color: var(--color-primary);
}

.table-option input[type="radio"]:checked ~ label {
    color: var(--color-primary);
    font-weight: var(--font-weight-bold);
}

.summary-section {
    background: rgba(39, 174, 96, 0.1);
    border-left: 4px solid var(--color-success);
    padding: var(--spacing-lg);
    border-radius: var(--border-radius-md);
    margin: var(--spacing-lg) 0;
}

.summary-section h3 {
    margin: 0 0 var(--spacing-md) 0;
    color: var(--color-primary);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid rgba(26, 71, 42, 0.1);
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item span {
    color: var(--color-dark);
}

.summary-item strong {
    color: var(--color-primary);
}
```

**JavaScript Implementation (in `public/js/reservations.js`):**
```javascript
// Handle "Check Availability" button
document.getElementById('checkAvailabilityBtn')?.addEventListener('click', async function(e) {
    e.preventDefault();
    
    const partySize = document.getElementById('party_size').value;
    const date = document.getElementById('reserved_at').value;
    const time = document.getElementById('time').value;
    const duration = document.querySelector('input[name="duration_hours"]:checked')?.value;
    
    // Validate
    if (!date || !time || !duration) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    // Show loader, hide tables
    document.getElementById('availabilityLoader').style.display = 'block';
    document.getElementById('tablesSection').style.display = 'none';
    
    try {
        const response = await fetch('/api/check-availability', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                date,
                time,
                duration: parseInt(duration),
                party_size: parseInt(partySize)
            })
        });
        
        const data = await response.json();
        populateTables(data.available_tables);
        document.getElementById('availabilityLoader').style.display = 'none';
        document.getElementById('tablesSection').style.display = 'block';
    } catch (error) {
        console.error(error);
        alert('Erreur lors de la vérification');
    }
});

// Populate tables
function populateTables(tables) {
    const container = document.getElementById('availableTables');
    container.innerHTML = '';
    
    if (tables.length === 0) {
        container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: red;">Aucune table disponible pour ce créneau</p>';
        return;
    }
    
    tables.forEach(table => {
        const label = document.createElement('label');
        label.className = 'table-option';
        label.innerHTML = `
            <input type="radio" name="table_id" value="${table.id}" required>
            <label>${table.name} (${table.capacity} pers.)</label>
        `;
        container.appendChild(label);
    });
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}

// Update summary when table selected
document.addEventListener('change', function(e) {
    if (e.target.name === 'table_id') {
        updateSummary();
        document.getElementById('summarySection').style.display = 'block';
    }
});

function updateSummary() {
    const partySize = document.getElementById('party_size').value;
    const date = document.getElementById('reserved_at').value;
    const time = document.getElementById('time').value;
    const duration = document.querySelector('input[name="duration_hours"]:checked')?.value;
    const table = document.querySelector('input[name="table_id"]:checked')?.parentElement?.innerText;
    
    document.getElementById('summaryPartySize').textContent = partySize;
    document.getElementById('summaryDateTime').textContent = `${date} à ${time}`;
    document.getElementById('summaryDuration').textContent = `${duration}h`;
    document.getElementById('summaryTable').textContent = table || '-';
}
```

---

### 7. `app/Views/reservations/my-reservations.php`

**Purpose:** User's reservation list (USER ONLY)  
**Route:** `GET /reservations/my`  
**Features:** Filtered by user_id, status colors, chronological sorting

**HTML Structure:**
```html
<div class="reservations-container">
    <h1>📅 Mes Réservations</h1>
    
    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <p>Vous n'avez aucune réservation pour le moment</p>
            <a href="/reservations/create" class="btn btn-primary">🎯 Effectuer une réservation</a>
        </div>
    <?php else: ?>
        <div class="reservations-list">
            <?php foreach ($reservations as $reservation): ?>
                <div class="reservation-card">
                    <div class="reservation-header">
                        <h3>Table <?= htmlspecialchars($reservation['table_name']) ?></h3>
                        <span class="status-badge status-<?= htmlspecialchars($reservation['status']) ?>">
                            <?php 
                                $statuses = [
                                    'pending' => '⏳ En attente',
                                    'confirmed' => '✓ Confirmée',
                                    'completed' => '✓ Complétée',
                                    'cancelled' => '✗ Annulée'
                                ];
                                echo $statuses[$reservation['status']] ?? '';
                            ?>
                        </span>
                    </div>
                    
                    <div class="reservation-details">
                        <div class="detail-row">
                            <span class="label">Date et heure:</span>
                            <span class="value"><?= date('d/m/Y \à H:i', strtotime($reservation['reserved_at'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Durée:</span>
                            <span class="value"><?= htmlspecialchars($reservation['duration_hours']) ?>h</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Nombre de personnes:</span>
                            <span class="value"><?= htmlspecialchars($reservation['party_size']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Créneau:</span>
                            <span class="value">
                                <?= date('H:i', strtotime($reservation['reserved_at'])) ?> - 
                                <?= date('H:i', strtotime($reservation['reserved_at'] . ' +' . $reservation['duration_hours'] . ' hours')) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
```

**CSS Additions (in `reservations.css`):**
```css
.reservations-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
}

.reservation-card {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
    border-left: 4px solid var(--color-primary);
}

.reservation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-md);
}

.reservation-header h3 {
    margin: 0;
    color: var(--color-primary);
    font-size: var(--font-size-lg);
}

.status-pending {
    background: var(--color-warning);
    color: var(--color-lighter);
}

.status-confirmed {
    background: var(--color-info);
    color: var(--color-lighter);
}

.status-completed {
    background: var(--color-success);
    color: var(--color-lighter);
}

.status-cancelled {
    background: var(--color-danger);
    color: var(--color-lighter);
}

.reservation-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
}

.detail-row {
    display: flex;
    justify-content: space-between;
    font-size: var(--font-size-sm);
}

.detail-row .label {
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
}

.detail-row .value {
    color: var(--color-primary);
    font-weight: var(--font-weight-bold);
}
```

---

### 8. `app/Views/reservations/index.php`

**Purpose:** All reservations management (ADMIN ONLY)  
**Route:** `GET /reservations`  
**Features:** Table list, status filters, quick actions (confirm/cancel), daily planning view

**HTML Structure:**
```html
<div class="admin-reservations">
    <div class="admin-header">
        <h1>📊 Gestion des Réservations</h1>
        
        <div class="admin-filters">
            <!-- Date filter -->
            <div class="filter-group">
                <label for="filterDate">Filtrer par date:</label>
                <input type="date" id="filterDate" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            
            <!-- Status filter -->
            <div class="filter-group">
                <label for="filterStatus">Filtrer par statut:</label>
                <select id="filterStatus" class="form-control">
                    <option value="">Tous</option>
                    <option value="pending">En attente</option>
                    <option value="confirmed">Confirmée</option>
                    <option value="completed">Complétée</option>
                    <option value="cancelled">Annulée</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table view -->
    <div class="reservations-table-wrapper">
        <table class="reservations-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Table</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Durée</th>
                    <th>Personnes</th>
                    <th>Créneau</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr class="reservation-row status-<?= htmlspecialchars($res['status']) ?>">
                        <td><?= htmlspecialchars($res['user_name']) ?></td>
                        <td><?= htmlspecialchars($res['user_email']) ?></td>
                        <td><?= htmlspecialchars($res['table_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($res['reserved_at'])) ?></td>
                        <td><?= date('H:i', strtotime($res['reserved_at'])) ?></td>
                        <td><?= htmlspecialchars($res['duration_hours']) ?>h</td>
                        <td><?= htmlspecialchars($res['party_size']) ?></td>
                        <td>
                            <?= date('H:i', strtotime($res['reserved_at'])) ?> - 
                            <?= date('H:i', strtotime($res['reserved_at'] . ' +' . $res['duration_hours'] . ' hours')) ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= htmlspecialchars($res['status']) ?>">
                                <?php 
                                    $statuses = [
                                        'pending' => '⏳ Attente',
                                        'confirmed' => '✓ Confirmée',
                                        'completed' => '✓ Complétée',
                                        'cancelled' => '✗ Annulée'
                                    ];
                                    echo $statuses[$res['status']] ?? '';
                                ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <?php if ($res['status'] === 'pending'): ?>
                                <form method="POST" action="/reservations/<?= htmlspecialchars($res['id']) ?>/status" style="display: inline;">
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn btn-sm btn-success">✓ Confirmer</button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if (in_array($res['status'], ['pending', 'confirmed'])): ?>
                                <form method="POST" action="/reservations/<?= htmlspecialchars($res['id']) ?>/status" style="display: inline;">
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')">✗ Annuler</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Daily planning grid (optional: shows table occupancy) -->
    <div class="planning-section">
        <h2>📆 Planning des Tables</h2>
        <div class="planning-grid">
            <!-- Populate dynamically with JS or backend -->
        </div>
    </div>
</div>
```

**CSS Additions (in `reservations.css`):**
```css
.admin-reservations {
    padding: var(--spacing-lg);
}

.admin-header {
    margin-bottom: var(--spacing-lg);
}

.admin-header h1 {
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
}

.admin-filters {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.filter-group label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
}

.filter-group .form-control {
    min-width: 150px;
}

.reservations-table-wrapper {
    overflow-x: auto;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: var(--spacing-xl);
}

.reservations-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--color-lighter);
}

.reservations-table thead {
    background: var(--color-primary);
    color: var(--color-lighter);
}

.reservations-table th {
    padding: var(--spacing-md);
    text-align: left;
    font-weight: var(--font-weight-bold);
    font-size: var(--font-size-sm);
}

.reservations-table td {
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--color-border);
    font-size: var(--font-size-sm);
}

.reservations-table tbody tr:hover {
    background: var(--color-light);
}

.reservation-row.status-pending {
    border-left: 4px solid var(--color-warning);
}

.reservation-row.status-confirmed {
    border-left: 4px solid var(--color-info);
}

.reservation-row.status-completed {
    border-left: 4px solid var(--color-success);
}

.reservation-row.status-cancelled {
    border-left: 4px solid var(--color-danger);
}

.action-buttons {
    display: flex;
    gap: var(--spacing-xs);
}

.action-buttons form {
    display: inline;
}

.btn-success {
    background: var(--color-success);
    color: var(--color-lighter);
}

.btn-success:hover {
    background: #229954;
}

.planning-section {
    margin-top: var(--spacing-xl);
}

.planning-section h2 {
    color: var(--color-primary);
    margin-bottom: var(--spacing-lg);
}

.planning-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-lg);
}
```

---

## 🕹️ SESSIONS PAGES

### 9. `app/Views/sessions/dashboard.php`

**Purpose:** Real-time active sessions dashboard (ADMIN ONLY)  
**Route:** `GET /sessions/dashboard`  
**Features:** Table status grid, elapsed/remaining timers, overflow alerts, start/end buttons

**HTML Structure:**
```html
<div class="sessions-dashboard">
    <div class="dashboard-header">
        <h1>🕹️ Gestion des Sessions</h1>
        <div class="last-update">Dernière mise à jour: <span id="lastUpdate">--:--:--</span></div>
    </div>
    
    <div class="tables-grid">
        <?php foreach ($tables as $table): ?>
            <?php 
                // Check if table has active session
                $activeSession = null;
                foreach ($activeSessions as $session) {
                    if ($session['table_id'] === $table['id']) {
                        $activeSession = $session;
                        break;
                    }
                }
            ?>
            
            <div class="table-card <?= $activeSession ? 'occupied' : 'available' ?>" data-table-id="<?= htmlspecialchars($table['id']) ?>">
                <div class="table-header">
                    <h3><?= htmlspecialchars($table['name']) ?></h3>
                    <span class="table-capacity">👥 <?= htmlspecialchars($table['capacity']) ?></span>
                </div>
                
                <?php if ($activeSession): ?>
                    <!-- Occupied table -->
                    <div class="table-content occupied-content">
                        <div class="session-info">
                            <p><strong>🎲 Jeu:</strong> <?= htmlspecialchars($activeSession['game_name']) ?></p>
                            <p><strong>👤 Client:</strong> <?= htmlspecialchars($activeSession['user_name']) ?></p>
                        </div>
                        
                        <div class="session-timers">
                            <div class="timer-item">
                                <span class="timer-label">Début:</span>
                                <span class="timer-value"><?= date('H:i', strtotime($activeSession['started_at'])) ?></span>
                            </div>
                            <div class="timer-item">
                                <span class="timer-label">Durée réservée:</span>
                                <span class="timer-value"><?= htmlspecialchars($activeSession['duration_hours']) ?>h</span>
                            </div>
                            <div class="timer-item">
                                <span class="timer-label">Écoulé:</span>
                                <span class="timer-value" id="elapsed-<?= htmlspecialchars($activeSession['id']) ?>">--</span>
                            </div>
                            <div class="timer-item <?= $activeSession['is_overdue'] ? 'overdue' : '' ?>">
                                <span class="timer-label">Restant:</span>
                                <span class="timer-value" id="remaining-<?= htmlspecialchars($activeSession['id']) ?>">--</span>
                            </div>
                        </div>
                        
                        <?php if ($activeSession['is_overdue']): ?>
                            <div class="alert alert-danger">
                                ⚠️ La session dépasse la durée prévue!
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="/sessions/<?= htmlspecialchars($activeSession['id']) ?>/end" class="session-action">
                            <button type="submit" class="btn btn-danger btn-block">
                                ⏹️ Terminer la session
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Available table -->
                    <div class="table-content available-content">
                        <p class="available-text">✓ Table disponible</p>
                        <a href="/sessions/create?table_id=<?= htmlspecialchars($table['id']) ?>" class="btn btn-primary btn-block">
                            ▶️ Démarrer une session
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="/public/js/sessions.js"></script>
```

**CSS Requirements (in `sessions.css`):**
```css
.sessions-dashboard {
    padding: var(--spacing-lg);
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xl);
}

.dashboard-header h1 {
    margin: 0;
    color: var(--color-primary);
    font-size: var(--font-size-xxl);
}

.last-update {
    font-size: var(--font-size-sm);
    color: var(--color-dark);
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-lg);
}

.table-card {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all var(--transition-base);
}

.table-card.occupied {
    border-left: 4px solid var(--color-accent);
}

.table-card.available {
    border-left: 4px solid var(--color-success);
}

.table-header {
    background: var(--color-primary);
    color: var(--color-lighter);
    padding: var(--spacing-md);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h3 {
    margin: 0;
    font-size: var(--font-size-lg);
}

.table-capacity {
    font-size: var(--font-size-sm);
    background: rgba(255, 255, 255, 0.2);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--border-radius-sm);
}

.table-content {
    padding: var(--spacing-lg);
}

.occupied-content {
    background: rgba(212, 130, 63, 0.05);
}

.available-content {
    background: rgba(39, 174, 96, 0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 250px;
}

.available-text {
    color: var(--color-success);
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
    margin-bottom: var(--spacing-md);
}

.session-info {
    margin-bottom: var(--spacing-lg);
}

.session-info p {
    margin: var(--spacing-sm) 0;
    font-size: var(--font-size-sm);
    color: var(--color-dark);
}

.session-timers {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.timer-item {
    background: var(--color-light);
    padding: var(--spacing-md);
    border-radius: var(--border-radius-md);
    text-align: center;
}

.timer-item.overdue {
    background: rgba(231, 76, 60, 0.1);
    border: 2px solid var(--color-danger);
}

.timer-label {
    display: block;
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
    margin-bottom: var(--spacing-xs);
}

.timer-value {
    display: block;
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}

.timer-item.overdue .timer-value {
    color: var(--color-danger);
}

.session-action {
    margin-top: var(--spacing-lg);
}
```

**JavaScript (in `public/js/sessions.js`):**
```javascript
// Real-time timer updates
setInterval(() => {
    document.querySelectorAll('[data-table-id]').forEach(card => {
        // Recalculate timers
        // Update elapsed/remaining times
    });
    
    // Update last-update timestamp
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
}, 1000);
```

---

### 10. `app/Views/sessions/create.php`

**Purpose:** Start a new session (ADMIN ONLY)  
**Route:** `GET /sessions/create`  
**Features:** Reservation selector, game picker, table selector

**HTML Structure:**
```html
<div class="session-form-container">
    <h1>▶️ Démarrer une Session</h1>
    
    <form method="POST" action="/sessions" class="session-form">
        <!-- Reservation selector -->
        <div class="form-group">
            <label for="reservation_id" class="form-label">Sélectionner une réservation confirmée *</label>
            <select id="reservation_id" name="reservation_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($confirmedReservations as $res): ?>
                    <option value="<?= htmlspecialchars($res['id']) ?>">
                        <?= htmlspecialchars($res['user_name']) ?> - Table <?= htmlspecialchars($res['table_name']) ?> - <?= date('d/m/Y H:i', strtotime($res['reserved_at'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Game selector -->
        <div class="form-group">
            <label for="game_id" class="form-label">Sélectionner un jeu disponible *</label>
            <select id="game_id" name="game_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($availableGames as $game): ?>
                    <option value="<?= htmlspecialchars($game['id']) ?>">
                        <?= htmlspecialchars($game['name']) ?> (<?= htmlspecialchars($game['min_players']) ?>-<?= htmlspecialchars($game['max_players']) ?> joueurs)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Table selector (auto-filled from reservation, but can override) -->
        <div class="form-group">
            <label for="table_id" class="form-label">Table (auto-rempli) *</label>
            <select id="table_id" name="table_id" class="form-control" disabled>
                <option>--</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                ▶️ Démarrer la session
            </button>
            <a href="/sessions/dashboard" class="btn btn-secondary">Retour au dashboard</a>
        </div>
    </form>
</div>
```

**CSS (in `sessions.css`):**
```css
.session-form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: var(--spacing-lg);
}

.session-form-container h1 {
    text-align: center;
    color: var(--color-primary);
    margin-bottom: var(--spacing-xl);
}

.session-form {
    background: var(--color-lighter);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}
```

---

### 11. `app/Views/sessions/history.php`

**Purpose:** Session history/analytics (ADMIN ONLY)  
**Route:** `GET /sessions/history`  
**Features:** Complete session log, filters, duration stats

**HTML Structure:**
```html
<div class="sessions-history">
    <h1>📊 Historique des Sessions</h1>
    
    <!-- Filters -->
    <div class="history-filters">
        <div class="filter-group">
            <label for="filterDate">Filtrer par date:</label>
            <input type="date" id="filterDate" class="form-control">
        </div>
        <div class="filter-group">
            <label for="filterGame">Filtrer par jeu:</label>
            <select id="filterGame" class="form-control">
                <option value="">Tous les jeux</option>
                <?php foreach ($games as $game): ?>
                    <option value="<?= htmlspecialchars($game['id']) ?>"><?= htmlspecialchars($game['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="filterTable">Filtrer par table:</label>
            <select id="filterTable" class="form-control">
                <option value="">Toutes les tables</option>
                <?php foreach ($tables as $table): ?>
                    <option value="<?= htmlspecialchars($table['id']) ?>"><?= htmlspecialchars($table['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <!-- History table -->
    <div class="history-table-wrapper">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Jeu</th>
                    <th>Table</th>
                    <th>Date</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Durée réservée</th>
                    <th>Durée réelle</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?= htmlspecialchars($session['user_name']) ?></td>
                        <td><?= htmlspecialchars($session['game_name']) ?></td>
                        <td><?= htmlspecialchars($session['table_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($session['started_at'])) ?></td>
                        <td><?= date('H:i', strtotime($session['started_at'])) ?></td>
                        <td><?= date('H:i', strtotime($session['ended_at'])) ?></td>
                        <td><?= htmlspecialchars($session['duration_hours']) ?>h</td>
                        <td><?= calculateDuration($session['started_at'], $session['ended_at']) ?></td>
                        <td>
                            <span class="status-badge status-<?= $session['is_overdue'] ? 'warning' : 'success' ?>">
                                <?= $session['is_overdue'] ? '⚠️ Dépassée' : '✓ Normal' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

**CSS (in `sessions.css`):**
```css
.sessions-history {
    padding: var(--spacing-lg);
}

.sessions-history h1 {
    color: var(--color-primary);
    margin-bottom: var(--spacing-lg);
}

.history-filters {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    flex-wrap: wrap;
}

.history-filters .filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.history-filters label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-dark);
}

.history-filters .form-control {
    min-width: 150px;
}

.history-table-wrapper {
    overflow-x: auto;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--color-lighter);
}

.history-table thead {
    background: var(--color-primary);
    color: var(--color-lighter);
}

.history-table th,
.history-table td {
    padding: var(--spacing-md);
    text-align: left;
    font-size: var(--font-size-sm);
}

.history-table th {
    font-weight: var(--font-weight-bold);
}

.history-table tbody tr:hover {
    background: var(--color-light);
}

.history-table tbody tr:nth-child(even) {
    background: var(--color-light);
}
```

---

## 🔧 SHARED COMPONENTS

### 12. `app/Views/layouts/header.php`

**Purpose:** Shared header (renders different nav for admin/user/guest)

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? '🎲 Aji L3bo Café' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/auth.css">
    <link rel="stylesheet" href="/public/css/games.css">
    <link rel="stylesheet" href="/public/css/reservations.css">
    <link rel="stylesheet" href="/public/css/sessions.css">
    <link rel="stylesheet" href="/public/css/responsive.css">
</head>
<body>
    <nav class="navbar">
        <!-- Logo/Brand -->
        <div class="navbar-brand">
            <a href="/" class="brand-link">🎲 Aji L3bo Café</a>
        </div>
        
        <!-- Nav menu -->
        <div class="navbar-menu">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <!-- Guest nav -->
                <a href="/games" class="nav-link">Jeux</a>
                <a href="/login" class="nav-link nav-link-primary">Connexion</a>
                <a href="/register" class="nav-link nav-link-secondary">Inscription</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <!-- Admin nav -->
                <a href="/sessions/dashboard" class="nav-link">Dashboard</a>
                <a href="/games" class="nav-link">Jeux</a>
                <a href="/reservations" class="nav-link">Réservations</a>
                <a href="/sessions/history" class="nav-link">Historique</a>
                <form method="POST" action="/logout" class="nav-form">
                    <button type="submit" class="nav-link nav-link-danger">Déconnexion</button>
                </form>
            <?php else: ?>
                <!-- Client nav -->
                <a href="/games" class="nav-link">Jeux</a>
                <a href="/reservations/create" class="nav-link">Réserver</a>
                <a href="/reservations/my" class="nav-link">Mes réservations</a>
                <form method="POST" action="/logout" class="nav-form">
                    <button type="submit" class="nav-link nav-link-danger">Déconnexion</button>
                </form>
            <?php endif; ?>
        </div>
    </nav>
```

**CSS for header (in `main.css`):**
```css
.navbar {
    background: var(--color-primary);
    color: var(--color-lighter);
    padding: var(--spacing-md) var(--spacing-lg);
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-md);
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar-brand {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
}

.brand-link {
    color: var(--color-lighter);
    text-decoration: none;
}

.navbar-menu {
    display: flex;
    gap: var(--spacing-lg);
    align-items: center;
}

.nav-link {
    color: var(--color-lighter);
    text-decoration: none;
    font-weight: var(--font-weight-medium);
    transition: all var(--transition-base);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--border-radius-sm);
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
}

.nav-link-primary {
    background: var(--color-accent);
    color: var(--color-lighter);
}

.nav-link-primary:hover {
    background: var(--color-accent-dark);
}

.nav-link-secondary {
    border: 2px solid var(--color-lighter);
}

.nav-link-danger {
    background: var(--color-danger);
}

.nav-form {
    margin: 0;
    padding: 0;
}

.nav-form .nav-link {
    background: none;
    border: none;
    cursor: pointer;
}
```

---

### 13. `app/Views/layouts/footer.php`

```php
    </main>
    
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2026 Aji L3bo Café. Tous droits réservés.</p>
            <p>Système de gestion digitale des réservations et sessions</p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="/public/js/main.js"></script>
    <script src="/public/js/reservations.js"></script>
    <script src="/public/js/sessions.js"></script>
</body>
</html>
```

**CSS for footer (in `main.css`):**
```css
.footer {
    background: var(--color-primary);
    color: var(--color-lighter);
    text-align: center;
    padding: var(--spacing-lg);
    margin-top: var(--spacing-xxl);
}

.footer-content p {
    margin: var(--spacing-xs) 0;
    font-size: var(--font-size-sm);
}
```

---

## 📱 RESPONSIVE DESIGN

Create `public/css/responsive.css`:

```css
/* Tablet: 768px and below */
@media (max-width: 768px) {
    .games-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .game-detail-wrapper {
        grid-template-columns: 1fr;
    }
    
    .game-detail-sidebar {
        position: static;
    }
    
    .navbar-menu {
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }
    
    .reservations-table {
        font-size: var(--font-size-xs);
    }
    
    .tables-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    
    .duration-selector {
        grid-template-columns: 1fr;
    }
    
    .specs-grid {
        grid-template-columns: 1fr;
    }
    
    .reservation-details {
        grid-template-columns: 1fr;
    }
}

/* Mobile: 480px and below */
@media (max-width: 480px) {
    :root {
        --spacing-lg: 1rem;
        --spacing-xl: 1.5rem;
        --font-size-xxl: 1.5rem;
    }
    
    .games-grid {
        grid-template-columns: 1fr;
    }
    
    .game-detail-wrapper {
        gap: var(--spacing-md);
    }
    
    .navbar {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .navbar-menu {
        flex-direction: column;
        width: 100%;
    }
    
    .nav-link {
        width: 100%;
        text-align: center;
    }
    
    .tables-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-filters {
        flex-direction: column;
    }
    
    .filter-group .form-control {
        min-width: auto;
        width: 100%;
    }
    
    .history-filters {
        flex-direction: column;
    }
}
```

---

## ✅ CRITICAL IMPLEMENTATION NOTES

### 1. **Authentication Check**
- All pages except `/login`, `/register`, `/games`, `/games/{id}` must check `$_SESSION['user_id']`
- Admin routes must check `$_SESSION['role'] === 'admin'`
- Use controller-level guards, not view-level

### 2. **Security**
- ALL user output → `htmlspecialchars($var)`
- Form CSRF tokens (if implementing)
- SQL injections handled in Model layer (PDO prepared statements)

### 3. **Data Formatting**
```php
// Dates
date('d/m/Y', strtotime($dateStr))      // 13/04/2026
date('H:i', strtotime($timeStr))        // 14:30

// Duration calculation
function calculateDuration($start, $end) {
    $diff = strtotime($end) - strtotime($start);
    return round($diff / 3600, 2) . 'h';
}
```

### 4. **CSS Class Naming Convention (BEM-like)**
- `.btn-primary` for buttons
- `.status-badge` for badges
- `.form-control` for inputs
- `.game-card` for card containers
- `.admin-panel` for admin sections

### 5. **Accessibility**
- All form inputs have `<label>` with matching `for=` attribute
- Use `aria-label` on icon buttons
- Use semantic HTML: `<button>`, `<form>`, `<fieldset>`, `<legend>`
- Color not sole indicator (use text + color)

### 6. **JavaScript Requirements**
- **NO jQuery** — vanilla JS only
- Check for element existence before manipulating
- Use `data-*` attributes for passing server data to JS
- Debounce filters/searches

### 7. **Admin vs User Separation**
- Pages visible by BOTH roles:  
  `/games` (list), `/games/{id}` (detail), `/reservations/create`
- **Admin ONLY**:  
  `/games/create`, `/games/{id}/edit`, `/games/{id}/delete`,  
  `/reservations` (all list), `/sessions/*`
- **User ONLY**:  
  `/reservations/my`

---

## 🚀 Implementation Checklist

Before submitting, verify:

- [ ] All 11 view files created
- [ ] All CSS files created (main, auth, games, reservations, sessions, responsive)
- [ ] Navbar correctly renders admin/user/guest navigation
- [ ] All forms use POST method for state-changing actions
- [ ] Status badges use correct colors per state
- [ ] Tables are responsive (horizontal scroll on mobile)
- [ ] All buttons are accessible (proper `type`, `aria-label` on icons)
- [ ] CSS variables used throughout (no hardcoded colors)
- [ ] Error messages visible and styled
- [ ] Loading states implemented (spinners for AJAX)
- [ ] Summary sections match data flow
- [ ] Responsive design tested at 480px, 768px, 1200px
- [ ] All form fields have proper validation attributes
- [ ] Footer appears on every page

---

*End of UI Implementation Guide*

Generated for Aji L3bo Café freelance project — April 2026
