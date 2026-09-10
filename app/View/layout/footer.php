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
            }
        });
    }
}());
</script>
</body>
</html>
