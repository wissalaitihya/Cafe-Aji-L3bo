<?php require __DIR__ . '/../layout/header.php'; ?>

<?php
// Helper: render N filled + (5-N) empty stars
function renderStars(float $avg, int $total = 0, bool $big = false): string {
    $cls = $big ? 'stars stars-big' : 'stars';
    $out = '<span class="' . $cls . '">';
    for ($i = 1; $i <= 5; $i++) {
        if ($avg >= $i)      $out .= '<span class="star filled">★</span>';
        elseif ($avg >= $i - 0.5) $out .= '<span class="star half">★</span>';
        else                 $out .= '<span class="star empty">★</span>';
    }
    $out .= '</span>';
    if ($total > 0) $out .= ' <span class="rating-count">(' . $total . ')</span>';
    return $out;
}
?>

<div class="page-header">
    <a href="<?= BASE_PATH ?>/games" class="btn-back">&#8592; Games</a>
    <h1>&#127918; <?= htmlspecialchars($game['name_game']) ?></h1>
</div>

<?php if (!empty($_GET['rated'])): ?>
    <div class="alert alert-success">&#11088; Thanks for your rating!</div>
<?php endif; ?>
<?php if (!empty($_GET['rating_error'])): ?>
    <div class="alert alert-error">Invalid rating. Please select 1–5 stars.</div>
<?php endif; ?>

<div class="detail-card">
    <?php if (!empty($game['image_game'])): ?>
        <div class="detail-image">
            <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($game['image_game']) ?>"
                 alt="<?= htmlspecialchars($game['name_game']) ?>">
        </div>
    <?php else: ?>
        <div class="detail-image-placeholder">&#127918;</div>
    <?php endif; ?>

    <div class="detail-top-row">
        <?php if ($isPlayingNow ?? false): ?>
            <span class="badge-playing">&#127918; You&rsquo;re playing this now!</span>
        <?php endif; ?>
        <span class="badge badge-<?= $game['status_game'] === 'available' ? 'success' : 'warning' ?>">
            <?= $game['status_game'] === 'available' ? 'Available' : 'In Use' ?>
        </span>
        <?php if ($ratingSummary['total'] > 0): ?>
            <div class="rating-summary-inline">
                <?= renderStars($ratingSummary['avg'], $ratingSummary['total'], true) ?>
                <span class="rating-avg-num"><?= number_format($ratingSummary['avg'], 1) ?> / 5</span>
            </div>
        <?php else: ?>
            <span class="muted" style="font-size:0.85rem">No ratings yet</span>
        <?php endif; ?>
    </div>

    <table class="detail-table">
        <tr><th>Category</th><td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $game['category_game']))) ?></td></tr>
        <tr><th>Players</th><td><?= $game['players_min'] ?> &ndash; <?= $game['players_max'] ?></td></tr>
        <tr><th>Duration</th><td><?= $game['duration'] ?> minutes</td></tr>
        <tr><th>Difficulty</th><td><?= ucfirst(htmlspecialchars($game['difficulty'])) ?></td></tr>
    </table>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($game['description_game'] ?? 'No description')) ?></p>

    <h3>🎯 How to Play</h3>
    <div class="how-to-play">
        <?php if (!empty($game['how_to_play'])): ?>
            <?= nl2br(htmlspecialchars($game['how_to_play'])) ?>
        <?php else: ?>
            <span class="muted">No instructions added yet.<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?> <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/edit">Add instructions →</a><?php endif; ?></span>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="card-actions">
            <a href="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/edit" class="btn btn-warning">&#9998; Edit</a>
            <form action="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Delete this game?')">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- ── Rating Section ── -->
<div class="rating-section">
    <h2>&#11088; Player Ratings</h2>

    <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_role'] !== 'admin'): ?>
        <?php if ($canRate): ?>
            <div class="rate-form-card">
                <h3><?= $userRating ? 'Update your rating' : 'Rate this game' ?></h3>
                <?php if ($isPlayingNow ?? false): ?>
                    <p class="rating-hint" style="color:var(--purple)">&#127918; You can rate while playing!</p>
                <?php endif; ?>
                <form action="<?= BASE_PATH ?>/games/<?= $game['id_game'] ?>/rate" method="POST" id="rate-form">
                    <!-- Half-star picker: 5 stars, each split into left-half (n-0.5) and right-half (n) -->
                    <div class="half-star-picker" id="half-star-picker">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star-unit" data-n="<?= $i ?>">&#9733;</span>
                        <?php endfor; ?>
                        <input type="hidden" name="stars" id="stars-input"
                               value="<?= $userRating ? $userRating['stars'] : '' ?>">
                    </div>
                    <div class="star-value-label" id="star-value-label">
                        <?= $userRating ? number_format((float)$userRating['stars'], 1) . ' / 5' : 'Click a star to rate' ?>
                    </div>
                    <textarea name="comment_rating" rows="2" placeholder="Optional comment…" class="rating-comment"><?= htmlspecialchars($userRating['comment_rating'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-success">&#11088; <?= $userRating ? 'Update Rating' : 'Submit Rating' ?></button>
                </form>
            </div>
        <?php elseif ($hasPlayed === false): ?>
            <p class="muted rating-hint">&#128274; Play this game first to leave a rating.</p>
        <?php endif; ?>
    <?php elseif (empty($_SESSION['user_id'])): ?>
        <p class="muted rating-hint"><a href="<?= BASE_PATH ?>/login">Log in</a> and play this game to rate it.</p>
    <?php endif; ?>

    <?php if (!empty($recentRatings)): ?>
        <div class="rating-list">
            <?php foreach ($recentRatings as $rv): ?>
                <div class="rating-row">
                    <div class="rating-row-top">
                        <strong><?= htmlspecialchars($rv['name_user']) ?></strong>
                        <?= renderStars((float)$rv['stars']) ?>
                        <span class="muted" style="font-size:0.8rem"><?= date('M j, Y', strtotime($rv['rated_at'])) ?></span>
                    </div>
                    <?php if (!empty($rv['comment_rating'])): ?>
                        <p class="rating-comment-text"><?= nl2br(htmlspecialchars($rv['comment_rating'])) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">No ratings yet. Be the first!</p>
    <?php endif; ?>
</div>

<?php if (!empty($related)): ?>
<div class="related-section">
    <h2>&#127918; More in <em><?= htmlspecialchars(ucwords(str_replace('_', ' ', $game['category_game']))) ?></em></h2>
    <div class="card-grid related-grid">
        <?php foreach ($related as $r): ?>
            <div class="card game-card">
                <?php if (!empty($r['image_game'])): ?>
                    <div class="card-image">
                        <img src="<?= BASE_PATH ?>/<?= htmlspecialchars($r['image_game']) ?>"
                             alt="<?= htmlspecialchars($r['name_game']) ?>">
                    </div>
                <?php else: ?>
                    <div class="card-image-placeholder">&#127918;</div>
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= htmlspecialchars($r['name_game']) ?></h3>
                    <div class="game-info-row">
                        <span>&#128101; <?= $r['players_min'] ?>–<?= $r['players_max'] ?></span>
                        <span>&#9200; <?= $r['duration'] ?>m</span>
                        <span class="badge badge-<?= $r['difficulty'] === 'easy' ? 'success' : ($r['difficulty'] === 'hard' ? 'danger' : 'warning') ?>"><?= ucfirst($r['difficulty']) ?></span>
                    </div>
                    <div class="card-actions">
                        <a href="<?= BASE_PATH ?>/games/<?= $r['id_game'] ?>" class="btn btn-small">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
(function() {
    var picker = document.getElementById('half-star-picker');
    if (!picker) return;

    var units  = picker.querySelectorAll('.star-unit');
    var input  = document.getElementById('stars-input');
    var label  = document.getElementById('star-value-label');
    var current = parseFloat(input.value) || 0;

    function setDisplay(val) {
        units.forEach(function(u) {
            var n = parseInt(u.dataset.n);
            u.className = 'star-unit' + (val >= n ? ' filled' : val >= n - 0.5 ? ' half' : ' empty');
        });
        label.textContent = val > 0 ? val.toFixed(1) + ' / 5' : 'Click a star to rate';
    }

    picker.addEventListener('mousemove', function(e) {
        var unit = e.target.closest('.star-unit');
        if (!unit) return;
        var rect = unit.getBoundingClientRect();
        var isLeft = (e.clientX - rect.left) < rect.width / 2;
        var n = parseInt(unit.dataset.n);
        setDisplay(isLeft ? n - 0.5 : n);
    });

    picker.addEventListener('mouseleave', function() {
        setDisplay(parseFloat(input.value) || 0);
    });

    picker.addEventListener('click', function(e) {
        var unit = e.target.closest('.star-unit');
        if (!unit) return;
        var rect = unit.getBoundingClientRect();
        var isLeft = (e.clientX - rect.left) < rect.width / 2;
        var n = parseInt(unit.dataset.n);
        var val = isLeft ? n - 0.5 : n;
        input.value = val;
        setDisplay(val);
    });

    setDisplay(current);
}());
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>