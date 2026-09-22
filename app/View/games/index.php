<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ── Hero Section ── -->
<section class="hero catalogue-hero">
    <div class="hero-glow hero-glow-a"></div>
    <div class="hero-glow hero-glow-b"></div>
    <div class="hero-inner">
        <div class="hero-copy">
            <p class="hero-eyebrow"><span class="hero-eyebrow-dot" aria-hidden="true"></span>Casablanca's Board Game Café</p>
            <h1 class="hero-title">Play.<br><span class="hero-title-accent">Laugh.</span> Repeat.</h1>
            <p class="hero-sub">From social deduction to cooperative quests — grab your squad, pick a table, and let the games begin.</p>
            <div class="hero-actions">
                <a href="<?= BASE_PATH ?>/games" class="btn btn-primary btn-hero">&#127918; Browse Games</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_PATH ?>/reservations/create" class="btn btn-hero btn-outline">&#128197; Book a Table</a>
                <?php else: ?>
                    <a href="<?= BASE_PATH ?>/register" class="btn btn-hero btn-outline">&#127881; Create Account</a>
                <?php endif; ?>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><span class="hero-stat-num"><?= (int)($heroStats['games'] ?? count($games)) ?></span><span class="hero-stat-label">Board Games</span></div>
                <div class="hero-stat"><span class="hero-stat-num"><?= (int)($heroStats['tables'] ?? 0) ?></span><span class="hero-stat-label">Comfortable Tables</span></div>
                <div class="hero-stat"><span class="hero-stat-num">2+</span><span class="hero-stat-label">Players per Game</span></div>
                <div class="hero-stat"><span class="hero-stat-num">24/7</span><span class="hero-stat-label">Game Service</span></div>
            </div>
        </div>
        <div class="hero-center" aria-label="Tonight at the café">
            <div class="hero-tonight">
                <p class="hero-tonight-kicker"><span class="hero-eyebrow-dot" aria-hidden="true"></span>Tonight at the café</p>
                <p class="hero-tonight-num"><?= (int)($heroStats['tables'] ?? 0) ?></p>
                <p class="hero-tonight-lbl">tables ready</p>
                <a href="<?= BASE_PATH ?>/reservations/availability" class="hero-tonight-link">See live availability &rarr;</a>
            </div>
            <ol class="hero-steps">
                <li><strong>01</strong> Pick a game</li>
                <li><strong>02</strong> Book a table</li>
                <li><strong>03</strong> Play &amp; repeat</li>
            </ol>
        </div>
        <div class="hero-visual" aria-hidden="true">
            <span class="hero-chip hero-chip-1"><strong><?= (int)($heroStats['games'] ?? count($games)) ?></strong> games on the shelf</span>
            <span class="hero-chip hero-chip-2"><strong><?= (int)($heroStats['tables'] ?? 0) ?></strong> tables ready tonight</span>
            <svg viewBox="0 0 600 460" fill="none" xmlns="http://www.w3.org/2000/svg" role="presentation">
                <defs>
                    <linearGradient id="hCardPurple" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#a855f7"/><stop offset="100%" stop-color="#7c3aed"/></linearGradient>
                    <linearGradient id="hCardCyan" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#06d6f5"/><stop offset="100%" stop-color="#0ea5e9"/></linearGradient>
                    <linearGradient id="hCardGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#fbbf24"/><stop offset="100%" stop-color="#f59e0b"/></linearGradient>
                    <radialGradient id="hGlow"><stop offset="0%" stop-color="#a855f7" stop-opacity="0.25"/><stop offset="100%" stop-color="#06d6f5" stop-opacity="0"/></radialGradient>
                    <filter id="hShadow" x="-30%" y="-30%" width="160%" height="180%"><feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#000" flood-opacity="0.16"/></filter>
                </defs>
                <circle cx="300" cy="220" r="200" fill="url(#hGlow)"/>

                <!-- game board backdrop -->
                <g class="hero-board" filter="url(#hShadow)">
                    <rect x="180" y="96" width="240" height="240" rx="22" fill="#fff" opacity=".92"/>
                    <g stroke="#ded9f5" stroke-width="2">
                        <path d="M200 140h200M200 180h200M200 220h200M200 260h200M200 300h200"/>
                        <path d="M200 140v200M240 140v200M280 140v200M320 140v200M360 140v200M400 140v200"/>
                    </g>
                    <rect x="200" y="140" width="40" height="40" fill="#a855f7" opacity=".14"/>
                    <rect x="280" y="180" width="40" height="40" fill="#06d6f5" opacity=".14"/>
                    <rect x="320" y="260" width="40" height="40" fill="#f59e0b" opacity=".18"/>
                    <rect x="240" y="220" width="40" height="40" fill="#a855f7" opacity=".14"/>
                    <rect x="360" y="140" width="40" height="40" fill="#06d6f5" opacity=".14"/>
                </g>

                <!-- back card -->
                <g class="hero-card hero-card-back" filter="url(#hShadow)">
                    <rect x="372" y="38" width="132" height="190" rx="16" fill="url(#hCardCyan)"/>
                    <rect x="385" y="51" width="106" height="164" rx="11" fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="2"/>
                    <path d="M438 116 L448 138 L438 160 L428 138Z" fill="#fff" opacity=".9"/>
                </g>
                <!-- front card -->
                <g class="hero-card hero-card-front" filter="url(#hShadow)">
                    <rect x="118" y="66" width="136" height="198" rx="16" fill="url(#hCardPurple)"/>
                    <rect x="131" y="79" width="110" height="172" rx="11" fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="2"/>
                    <path d="M186 116 L193 132 L211 132 L197 142 L202 159 L186 150 L170 159 L175 142 L161 132 L179 132Z" fill="#fff"/>
                    <circle cx="186" cy="182" r="13" fill="#06d6f5" opacity=".9"/>
                    <circle cx="212" cy="198" r="13" fill="#06d6f5" opacity=".55"/>
                    <circle cx="228" cy="234" r="13" fill="#06d6f5" opacity=".35"/>
                </g>
                <!-- gold card -->
                <g class="hero-card hero-card-gold" filter="url(#hShadow)">
                    <rect x="330" y="250" width="112" height="160" rx="16" fill="url(#hCardGold)"/>
                    <rect x="342" y="262" width="88" height="136" rx="11" fill="none" stroke="#fff" stroke-opacity=".4" stroke-width="2"/>
                    <path d="M386 296 L396 318 L386 340 L376 318Z" fill="#fff" opacity=".9"/>
                </g>

                <!-- die 1 -->
                <g class="hero-die hero-die-1" filter="url(#hShadow)">
                    <rect x="432" y="330" width="84" height="84" rx="18" fill="#fff"/>
                    <circle cx="455" cy="353" r="8.5" fill="#312e81"/><circle cx="494" cy="353" r="8.5" fill="#312e81"/>
                    <circle cx="474" cy="372" r="8.5" fill="#312e81"/>
                    <circle cx="455" cy="391" r="8.5" fill="#312e81"/><circle cx="494" cy="391" r="8.5" fill="#312e81"/>
                </g>
                <!-- die 2 -->
                <g class="hero-die hero-die-2" filter="url(#hShadow)">
                    <rect x="34"  y="292" width="62" height="62" rx="13" fill="#fff"/>
                    <circle cx="54" cy="312" r="7" fill="#a855f7"/><circle cx="77" cy="335" r="7" fill="#a855f7"/>
                </g>
                <!-- die 3 -->
                <g class="hero-die hero-die-3" filter="url(#hShadow)">
                    <rect x="268" y="392" width="56" height="56" rx="12" fill="#fff"/>
                    <circle cx="285" cy="409" r="6.5" fill="#06d6f5"/><circle cx="307" cy="431" r="6.5" fill="#06d6f5"/>
                    <circle cx="296" cy="420" r="6.5" fill="#f59e0b"/>
                </g>

                <!-- game tokens / meeples -->
                <g class="hero-token hero-token-1" filter="url(#hShadow)">
                    <circle cx="150" cy="278" r="20" fill="#a855f7"/>
                    <circle cx="150" cy="278" r="9" fill="#fff" opacity=".9"/>
                </g>
                <g class="hero-token hero-token-2" filter="url(#hShadow)">
                    <circle cx="207" cy="316" r="20" fill="#06d6f5"/>
                    <circle cx="207" cy="316" r="9" fill="#fff" opacity=".9"/>
                </g>
                <g class="hero-token hero-token-3" filter="url(#hShadow)">
                    <circle cx="318" cy="318" r="18" fill="#f59e0b"/>
                    <circle cx="318" cy="318" r="8" fill="#fff" opacity=".9"/>
                </g>

                <!-- sparkles -->
                <path class="spark spark-1" d="M56 78 l7 18 18 7 -18 7 -7 18 -7 -18 -18 -7 18 -7z" fill="#a855f7" opacity=".8"/>
                <path class="spark spark-2" d="M492 74 l5 13 13 5 -13 5 -5 13 -5 -13 -13 -5 13 -5z" fill="#06d6f5" opacity=".8"/>
                <path class="spark spark-3" d="M556 250 l4 10 10 4 -10 4 -4 10 -4 -10 -10 -4 10 -4z" fill="#f59e0b" opacity=".8"/>
                <path class="spark spark-4" d="M118 236 l3.5 9 9 3.5 -9 3.5 -3.5 9 -3.5 -9 -9 -3.5 9 -3.5z" fill="#06d6f5" opacity=".7"/>
            </svg>
        </div>
    </div>
</section>

<div class="page-header catalogue-heading">
    <div>
        <p class="eyebrow-label">THE LIBRARY</p>
        <h1>Choose your next table story</h1>
        <p class="page-intro">Browse the shelf, find your people, and make tonight a little more memorable.</p>
    </div>
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="<?= BASE_PATH ?>/games/create" class="btn btn-success">+ Add Game</a>
    <?php endif; ?>
</div>

<!-- Advanced Search / Filter -->
<form method="GET" action="<?= BASE_PATH ?>/games" class="search-bar" id="search-form">
    <div class="search-row">
        <div class="search-input-wrap">
            <span class="search-icon">&#128269;</span>
            <input type="text" name="q" placeholder="Search games by name or description…"
                   value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="search-input" autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if (array_filter($filters, function($v){ return $v !== ''; })): ?>
            <a href="<?= BASE_PATH ?>/games" class="btn btn-secondary">&#10005; Clear</a>
        <?php endif; ?>
    </div>
    <div class="filter-chips">
        <select name="category" class="filter-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach (['social_deduction'=>'Social Deduction','party'=>'Party','cooperative'=>'Cooperative','team'=>'Team','trivia'=>'Trivia','other'=>'Other'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($filters['category'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <select name="difficulty" class="filter-select" onchange="this.form.submit()">
            <option value="">Any Difficulty</option>
            <option value="easy"   <?= ($filters['difficulty'] ?? '') === 'easy'   ? 'selected' : '' ?>>&#128994; Easy</option>
            <option value="medium" <?= ($filters['difficulty'] ?? '') === 'medium' ? 'selected' : '' ?>>&#128992; Medium</option>
            <option value="hard"   <?= ($filters['difficulty'] ?? '') === 'hard'   ? 'selected' : '' ?>>&#128308; Hard</option>
        </select>
        <div class="filter-players-wrap">
            <label>&#128101; Players:</label>
            <input type="number" name="players" min="1" max="20"
                   value="<?= htmlspecialchars($filters['players'] ?? '') ?>"
                   placeholder="e.g. 4" class="filter-input-small">
        </div>
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>&#9989; Available</option>
            <option value="in_use"    <?= ($filters['status'] ?? '') === 'in_use'    ? 'selected' : '' ?>>&#128308; In Use</option>
        </select>
    </div>
</form>

<?php if (empty($games)): ?>
    <div class="empty-state"><p>No games found matching your search.</p></div>
<?php else: ?>
    <p class="results-count"><?= (int)($pagination['total'] ?? count($games)) ?> game<?= ((int)($pagination['total'] ?? count($games)) !== 1) ? 's' : '' ?> found</p>
    <div class="card-grid">
        <?php foreach ($games as $game): ?>
            <div class="card game-card">
                <div class="card-image">
                    <a href="<?= BASE_PATH ?>/games/<?= (int)$game['id_game'] ?>" class="game-image-link" aria-label="View <?= htmlspecialchars($game['name_game']) ?> details">
                        <img src="<?= game_image_url($game) ?>" alt="<?= htmlspecialchars($game['name_game']) ?>" loading="lazy" decoding="async" width="900" height="600">
                    </a>
                    <span class="game-card-category"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $game['category_game']))) ?></span>
                </div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($game['name_game']) ?></h3>
                    <div class="game-info-row">
                        <span>&#128101; <?= (int)$game['players_min'] ?>–<?= (int)$game['players_max'] ?></span>
                        <span>&#9200; <?= (int)$game['duration'] ?>m</span>
                        <span class="badge badge-<?= $game['difficulty'] === 'easy' ? 'success' : ($game['difficulty'] === 'hard' ? 'danger' : 'warning') ?>"><?= htmlspecialchars(ucfirst($game['difficulty'])) ?></span>
                    </div>
                    <span class="badge badge-<?= $game['status_game'] === 'available' ? 'success' : 'warning' ?>">
                        <?= $game['status_game'] === 'available' ? 'Available' : 'In Use' ?>
                    </span>
                    <div class="card-actions">
                        <a href="<?= BASE_PATH ?>/games/<?= (int)$game['id_game'] ?>" class="btn btn-small">Details</a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="<?= BASE_PATH ?>/games/<?= (int)$game['id_game'] ?>/edit" class="btn btn-small btn-warning">&#9998; Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($pagination) && ($pagination['totalPages'] ?? 1) > 1): ?>
        <nav class="pagination" aria-label="Games pages" style="display:flex;gap:.5rem;justify-content:center;margin:1.5rem 0">
            <?php
                $pg = (int)$pagination['page'];
                $tp = (int)$pagination['totalPages'];
                $base = BASE_PATH . '/games?' . http_build_query(array_filter([
                    'q' => $filters['q'] ?? null,
                    'category' => $filters['category'] ?? null,
                    'difficulty' => $filters['difficulty'] ?? null,
                    'players' => $filters['players'] ?? null,
                    'status' => $filters['status'] ?? null,
                ]));
                $sep = str_contains($base, '?') && strlen($base) > strlen(BASE_PATH . '/games?') ? '&' : ($base === BASE_PATH . '/games?' ? '' : '?');
                // http_build_query above already includes ?; normalize:
                $base = BASE_PATH . '/games';
                $qs = http_build_query(array_filter([
                    'q' => $filters['q'] ?? null,
                    'category' => $filters['category'] ?? null,
                    'difficulty' => $filters['difficulty'] ?? null,
                    'players' => $filters['players'] ?? null,
                    'status' => $filters['status'] ?? null,
                ]));
                $prefix = $base . ($qs ? '?' . $qs . '&' : '?');
            ?>
            <?php if ($pg > 1): ?>
                <a class="btn btn-small btn-secondary" href="<?= htmlspecialchars($prefix . 'page=' . ($pg - 1)) ?>">&larr; Prev</a>
            <?php endif; ?>
            <span class="muted">Page <?= $pg ?> / <?= $tp ?></span>
            <?php if ($pg < $tp): ?>
                <a class="btn btn-small btn-secondary" href="<?= htmlspecialchars($prefix . 'page=' . ($pg + 1)) ?>">Next &rarr;</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
