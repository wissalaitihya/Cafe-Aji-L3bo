# 🎯 Aji L3bo Café — Quick Reference for AI Coding Agent

## 📊 What You've Received

A **comprehensive 2,870-line UI implementation guide** that covers:

✅ **11 complete HTML/PHP view files** with full structure  
✅ **6 modular CSS files** with design system  
✅ **Complete admin/user separation** clearly marked  
✅ **JavaScript requirements** for dynamic features  
✅ **Accessibility standards** (ARIA, semantic HTML)  
✅ **Responsive design** (mobile-first, 480px-1200px+)  

---

## 📂 Files to Create

### HTML/PHP Views (in `app/Views/`)

| File | Purpose | Visibility | Status |
|---|---|---|---|
| `auth/login.php` | Login form | PUBLIC | ✅ Complete |
| `auth/register.php` | Registration form | PUBLIC | ✅ Complete |
| `games/index.php` | Game catalog | USER+ADMIN | ✅ Complete |
| `games/show.php` | Game detail | USER+ADMIN | ✅ Complete |
| `games/create.php` | Add game form | ADMIN ONLY | ✅ Complete |
| `games/edit.php` | Edit game form | ADMIN ONLY | ✅ Complete |
| `reservations/create.php` | Book reservation | USER | ✅ Complete |
| `reservations/my-reservations.php` | My bookings | USER | ✅ Complete |
| `reservations/index.php` | All reservations | ADMIN ONLY | ✅ Complete |
| `sessions/dashboard.php` | Live table dashboard | ADMIN ONLY | ✅ Complete |
| `sessions/create.php` | Start session | ADMIN ONLY | ✅ Complete |
| `sessions/history.php` | Session history | ADMIN ONLY | ✅ Complete |
| `layouts/header.php` | Top navbar (role-aware) | SHARED | ✅ Complete |
| `layouts/footer.php` | Footer | SHARED | ✅ Complete |

### CSS Files (in `public/css/`)

| File | Purpose | Lines |
|---|---|---|
| `main.css` | CSS variables + global styles | ~200 |
| `auth.css` | Login/register styling | ~150 |
| `games.css` | Game catalog + detail | ~250 |
| `reservations.css` | Booking pages styling | ~200 |
| `sessions.css` | Dashboard + history | ~250 |
| `responsive.css` | Mobile breakpoints | ~100 |

### JavaScript Files (in `public/js/`)

| File | Purpose |
|---|---|
| `main.js` | Global utilities |
| `reservations.js` | Availability checker + dynamic tables |
| `sessions.js` | Real-time timer updates |

---

## 🎨 Design System

All colors, spacing, and typography are **CSS variables**:

```css
:root {
    --color-primary: #1a472a;           /* Forest green */
    --color-accent: #d4823f;            /* Terracotta */
    --color-success: #27ae60;           /* Green */
    --color-warning: #f39c12;           /* Orange */
    --color-danger: #e74c3c;            /* Red */
    --color-info: #3498db;              /* Blue */
}
```

---

## 🔐 Admin vs User Pages

### 🟢 User-Facing (Clients)
- `/login` - Public
- `/register` - Public
- `/games` - Browse all games
- `/games/{id}` - View game details
- `/reservations/create` - Make a booking
- `/reservations/my` - View own bookings

### 🔴 Admin-Only Pages
- `/games/create` - Add new game
- `/games/{id}/edit` - Edit game
- `/games/{id}/delete` - Remove game
- `/reservations` - Manage all bookings
- `/sessions/dashboard` - Real-time table status
- `/sessions/create` - Start a session
- `/sessions/history` - View session logs

---

## 🔑 Key Implementation Details

### 1. **Authentication Check**
```php
// In controller, before rendering admin pages:
if ($_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}
```

### 2. **Navigation (Role-Aware)**
The `header.php` file checks `$_SESSION['role']` and renders different menus:
- **Guest** (no session) → Login/Register links only
- **Admin** → Dashboard, Games, Reservations, History
- **User** → Games, Create Reservation, My Reservations

### 3. **Security**
- ALL output uses `htmlspecialchars()`
- Forms use POST for state changes
- No hardcoded data in views

### 4. **Status Badges**
Classes like `status-available`, `status-pending`, `status-confirmed`, `status-cancelled` with color-coded backgrounds.

### 5. **Forms**
- All inputs have `<label>` with matching `for=` attribute
- Validation attributes: `required`, `min`, `max`, `pattern`
- Error messages displayed below each field
- Submit buttons disabled until form is valid

### 6. **Responsive Design**
- Mobile-first approach (default is mobile)
- Breakpoints: 480px (small mobile), 768px (tablet), 1200px+ (desktop)
- Grids use `auto-fill` + `minmax()` for flexibility

---

## 📋 CSS Class Naming Convention

```css
/* Buttons */
.btn                    /* Base button */
.btn-primary            /* Main action (green) */
.btn-secondary          /* Alternative action */
.btn-danger             /* Destructive (red) */
.btn-sm                 /* Small size */
.btn-block              /* Full width */

/* Status badges */
.status-badge           /* Base badge */
.status-available       /* Green background */
.status-pending         /* Orange background */
.status-confirmed       /* Blue background */
.status-completed       /* Green background */
.status-cancelled       /* Red background */

/* Forms */
.form-control           /* Input, select, textarea */
.form-group             /* Input wrapper */
.form-label             /* Label text */
.form-error             /* Error message */
.form-section           /* Fieldset equivalent */

/* Cards & Containers */
.card                   /* Generic card */
.game-card              /* Game listing card */
.reservation-card       /* Booking card */
.table-card             /* Session table card */

/* Admin sections */
.admin-panel            /* Admin controls */
.admin-only             /* Admin-only content */
```

---

## 🔧 JavaScript Integration Points

### Availability Checker (`reservations.js`)
- Listens for "Check Availability" button click
- Sends AJAX to `/api/check-availability`
- Populates available tables dynamically
- Enables submit button when table selected

### Real-time Dashboard (`sessions.js`)
- Updates every 1 second
- Recalculates elapsed/remaining time
- Shows overflow alerts
- Updates "last update" timestamp

---

## ✅ Before Implementation

1. **Database schema must exist** with all tables created
2. **Routes must be registered** in your router
3. **Controllers must pass data** to views (e.g., `$games`, `$reservations`)
4. **Models must query the database** correctly

---

## 🚀 Implementation Order Recommended

1. **Static pages first:**
   - `auth/login.php` → `auth/register.php`
   - `games/index.php` → `games/show.php`
   
2. **Admin forms:**
   - `games/create.php` → `games/edit.php`
   
3. **Complex interactive pages:**
   - `reservations/create.php` (needs JS + AJAX)
   - `sessions/dashboard.php` (needs real-time updates)
   
4. **Lists & tables:**
   - `reservations/index.php`
   - `reservations/my-reservations.php`
   - `sessions/history.php`
   
5. **Layout & styling:**
   - `layouts/header.php` → `layouts/footer.php`
   - All CSS files

---

## 📝 Validation & Submission Checklist

- [ ] All 14 PHP view files created
- [ ] All 6 CSS files created
- [ ] Header renders different navigation per role
- [ ] All forms have proper validation
- [ ] Error messages styled and visible
- [ ] Status badges use correct colors
- [ ] Tables are responsive
- [ ] Mobile layout works at 480px
- [ ] Admin pages are protected (role check)
- [ ] CSS variables used throughout
- [ ] No hardcoded values in HTML
- [ ] All user output escaped with `htmlspecialchars()`
- [ ] JavaScript handlers don't error (check console)
- [ ] Footer appears on all pages

---

## 🎯 Key Sections in Full Guide

### If you need detailed info, see:

- **Color scheme & spacing** → CSS Variables & Design System (section 7)
- **Each file's full HTML** → File-by-File Implementation (section 3)
- **CSS for each module** → Sections labeled "CSS Requirements"
- **JavaScript code** → Sections with `<script>` tags
- **Responsive rules** → "Responsive Design" section
- **Security best practices** → "Critical Implementation Notes"

---

## 📞 Quick Troubleshooting

**Q: Where do I check if user is admin?**  
A: In controller: `if ($_SESSION['role'] !== 'admin') redirect('/login');`

**Q: How do I show/hide admin-only buttons?**  
A: Wrap in PHP: `<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?> ... <?php endif; ?>`

**Q: What if a page needs data from multiple tables?**  
A: Pass it all in the controller: `$data = ['games' => $games, 'reservations' => $reservations];`

**Q: Can I modify the color scheme?**  
A: Yes! Update CSS variables in `main.css` `:root` block. All colors will update automatically.

**Q: Do I need to add CSRF tokens?**  
A: Guide includes form structure—add CSRF implementation in controller layer.

---

## 📎 File Statistics

- **Total lines of guide:** 2,870
- **HTML view templates:** 14 files
- **CSS modules:** 6 files  
- **JavaScript files:** 3 files
- **Documented CSS classes:** 40+
- **Form components:** 20+
- **Page mockups:** 11 complete

---

**Ready to implement!** Follow the guide section by section, and every page will have perfect admin/user separation. 🎯

Generated: April 2026 | Aji L3bo Café Freelance Project
