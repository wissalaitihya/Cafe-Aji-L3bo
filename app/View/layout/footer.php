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
    var btn = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('sidebar');
    if (!btn || !sidebar) return;
    var key = 'sidebar_collapsed';
    if (localStorage.getItem(key) === '1') sidebar.classList.add('collapsed');
    btn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem(key, sidebar.classList.contains('collapsed') ? '1' : '0');
    });
    document.addEventListener('click', function(e) {
        if (sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !e.target.closest('.topbar-menu-btn')) {
            sidebar.classList.remove('open');
        }
    });
}());
</script>
</body>
</html>
