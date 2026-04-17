# Changes Summary — Fix Routing, Paths & Auth Pages

## Problem

The app was broken with two main errors:

1. **500 Internal Server Error** — Apache infinite rewrite loop  
2. **404 after login/register** — All redirects and links pointed to wrong URLs  
3. **Login/Register pages had no styling** — They didn't use the shared layout  

---

## Root Cause

All URLs in controllers and views were **hardcoded** as `/Cafe-Aji-L3bo/...`, but the actual app URL is `/autoformationphp/Cafe-Aji-L3bo/...`. This mismatch caused every redirect and link to land on a 404 page.

---

## Changes Made

### 1. Dynamic `BASE_PATH` constant — `public/index.php`

**What:** Added a `define('BASE_PATH', ...)` that auto-detects the correct base URL.  
**Why:** Instead of hardcoding the path, it reads from `SCRIPT_NAME` so the app works no matter where it's deployed (`/Cafe-Aji-L3bo/`, `/autoformationphp/Cafe-Aji-L3bo/`, etc.).

```php
// Before: nothing — every file had its own hardcoded path
// After:
define('BASE_PATH', rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'));
```

---

### 2. Fixed `.htaccess` rewrite loop — `.htaccess`

**What:** The root `.htaccess` already had a guard rule (`RewriteRule ^public/ - [L]`) to prevent infinite loops. No second `.htaccess` inside `public/` is needed.  
**Why:** Without the guard, Apache kept rewriting `/public/something` → `/public/public/something` → ... until it hit the 10-redirect limit and returned a 500 error.

---

### 3. Updated `redirect()` in all 5 controllers

**Files changed:**
- `app/Controller/AuthController.php`
- `app/Controller/DashboardController.php`
- `app/Controller/GameController.php`
- `app/Controller/SessionController.php`
- `app/Controller/ReservationController.php`

**What:** Replaced hardcoded path with `BASE_PATH` constant.  
**Why:** Redirects after login, register, game creation, etc. were all going to wrong URLs.

```php
// Before:
header("Location: /Cafe-Aji-L3bo" . $url);

// After:
header("Location: " . BASE_PATH . $url);
```

---

### 4. Updated all view files (links and form actions)

**Files changed (15 files):**
- `app/View/layout/header.php` — navbar links + CSS path
- `app/View/error/404.php` — "Go Home" link
- `app/View/dashboard/admin.php` — admin quick links
- `app/View/dashboard/player.php` — player quick links
- `app/View/games/index.php` — category filters, game links
- `app/View/games/show.php` — back link, edit/delete actions
- `app/View/games/create.php` — form action, cancel link
- `app/View/games/edit.php` — form action, cancel link
- `app/View/Session/dashboard.php` — session links, end form
- `app/View/Session/create.php` — form action, cancel link
- `app/View/Session/history.php` — back link
- `app/View/reservation/create.php` — form action, JS fetch URLs
- `app/View/reservation/index.php` — status form actions
- `app/View/reservation/availability.php` — form action, book links
- `app/View/reservation/myreservations.php` — book link

**What:** Every `href="/Cafe-Aji-L3bo/..."` became `href="<?= BASE_PATH ?>/..."`.  
**Why:** Same reason as controllers — all links were pointing to wrong URLs.

---

### 5. Removed duplicate `session_start()` — `login.php`, `register.php`

**What:** Removed `session_start()` from both auth view files.  
**Why:** `public/index.php` already calls `session_start()`. Calling it again in views triggered a PHP notice: *"Ignoring session_start() because a session is already active"*.

---

### 6. Fixed login form — `app/View/auth/login.php`

Three bugs fixed:

| Bug | Before | After |
|-----|--------|-------|
| Form action | `/Cafe-Aji-L3bo/public/index.php?action=handleLogin` | `<?= BASE_PATH ?>/login` |
| Password field name | `name="pass_word"` | `name="password"` |
| Register link | `href="register.php"` | `href="<?= BASE_PATH ?>/register"` |

**Why:**  
- The form was bypassing the router entirely (posting to `index.php?action=...` instead of the `/login` route)
- The password field name didn't match what the controller reads (`$_POST['password']`), so login could never work
- The register link was a relative file path instead of a route

---

### 7. Fixed register form — `app/View/auth/register.php`

| Bug | Before | After |
|-----|--------|-------|
| Form action | `/index.php?action=handleRegister` | `<?= BASE_PATH ?>/register` |
| Login link | `href="login.php"` | `href="<?= BASE_PATH ?>/login"` |

**Why:** Same as login — was bypassing the router.

---

### 8. Auth pages now use shared layout — `login.php`, `register.php`

**What:** Replaced standalone `<!DOCTYPE html>...<body>` with `require header.php` and `require footer.php`.  
**Why:** These were the only pages without the shared layout, so they had no CSS (no styling), no navbar, and no footer. Also fixed broken HTML (mismatched `<div>` tags, duplicate `</body>` tags).

---

## How to Test

1. Go to `http://localhost/autoformationphp/Cafe-Aji-L3bo/`
2. Login and register pages should now have full styling (navbar, CSS)
3. Login should redirect to the correct dashboard (admin or player)
4. All navigation links should work without 404 errors
5. Game CRUD, reservations, and sessions should all work correctly
