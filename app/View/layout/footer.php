<?php $userRole = $_SESSION['user_role'] ?? 'guest'; ?>

<?php if ($userRole === 'guest'): ?>
    </main><!-- /.public-content -->
    <footer class="public-footer">
        <p>&copy; <?= date('Y') ?> Aji L3bo Café &mdash; Casablanca</p>
    </footer>
</div><!-- /.public-layout -->
<?php else: ?>
        </main><!-- /.content -->
        <footer class="app-footer">
            <p>&copy; <?= date('Y') ?> Aji L3bo Café &mdash; Casablanca</p>
        </footer>
    </div><!-- /.main-wrapper -->
</div><!-- /.app-layout -->
<?php endif; ?>

<script>
(function(){
    /* Public nav dropdown toggle (mobile) */
    var navToggle = document.getElementById('public-nav-toggle');
    var nav = document.getElementById('public-nav');
    if (navToggle && nav) {
        navToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = nav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.addEventListener('click', function(e) {
            if (nav.classList.contains('open') && !nav.contains(e.target) && !navToggle.contains(e.target)) {
                nav.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
    /* Sidebar collapse toggle */
    var btn = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('sidebar');
    var logo = document.querySelector('.sidebar-logo');
    var menuButton = document.getElementById('topbar-menu');
    if (menuButton && sidebar) {
        menuButton.addEventListener('click', function() {
            var open = sidebar.classList.toggle('open');
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
    if (sidebar) {
        var key = 'sidebar_collapsed';
        if (localStorage.getItem(key) === '1') sidebar.classList.add('collapsed');
        if (btn) {
            btn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem(key, sidebar.classList.contains('collapsed') ? '1' : '0');
            });
        }
        /* When collapsed, clicking the logo re-expands the sidebar */
        if (logo) {
            logo.addEventListener('click', function(e) {
                if (sidebar.classList.contains('collapsed')) {
                    e.preventDefault();
                    sidebar.classList.remove('collapsed');
                    localStorage.setItem(key, '0');
                }
            });
        }
        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !e.target.closest('.topbar-menu-btn')) {
                sidebar.classList.remove('open');
                if (menuButton) menuButton.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* Header search suggestions: results appear while typing, without a submit. */
    document.querySelectorAll('.global-search').forEach(function(form) {
        var input = form.querySelector('input[type="search"]');
        var results = form.querySelector('.global-search-results');
        var timer = null;
        var request = null;
        if (!input || !results) return;

        function clearResults() {
            results.replaceChildren();
            results.setAttribute('hidden', '');
        }

        function showResults(games) {
            results.replaceChildren();
            if (!games.length) {
                var empty = document.createElement('div');
                empty.className = 'global-search-empty';
                empty.textContent = 'No games found';
                results.appendChild(empty);
            } else {
                games.slice(0, 6).forEach(function(game) {
                    var link = document.createElement('a');
                    link.className = 'global-search-result';
                    link.href = game.href;
                    link.textContent = game.name;
                    results.appendChild(link);
                });
            }
            results.removeAttribute('hidden');
        }

        input.addEventListener('input', function() {
            var query = input.value.trim();
            window.clearTimeout(timer);
            if (request) request.abort();
            if (query.length < 2) {
                clearResults();
                return;
            }
            timer = window.setTimeout(function() {
                var base = '<?= BASE_PATH ?>/api/games/search';
                var url = base + '?q=' + encodeURIComponent(query);
                request = new AbortController();
                fetch(url, { signal: request.signal, headers: { 'Accept': 'application/json' } })
                    .then(function(response) { return response.json(); })
                    .then(function(games) {
                        showResults(games.slice(0, 6));
                    })
                    .catch(function(error) {
                        if (error.name !== 'AbortError') clearResults();
                    });
            }, 220);
        });

        document.addEventListener('click', function(e) {
            if (!form.contains(e.target)) clearResults();
        });
    });

    /* Profile popover uses the existing session identity without adding a backend route. */
    var profileTrigger = document.getElementById('profile-trigger');
    var profilePopover = document.getElementById('profile-popover');
    if (profileTrigger && profilePopover) {
        profileTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = profilePopover.hasAttribute('hidden');
            if (open) profilePopover.removeAttribute('hidden');
            else profilePopover.setAttribute('hidden', '');
            profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.addEventListener('click', function(e) {
            if (!profilePopover.hasAttribute('hidden') && !profilePopover.contains(e.target) && !profileTrigger.contains(e.target)) {
                profilePopover.setAttribute('hidden', '');
                profileTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
}());
</script>
</body>
</html>
