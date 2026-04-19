<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <a href="<?= BASE_PATH ?>/admin/dashboard" class="btn-back">&#8592; Dashboard</a>
        <h1>&#127918; Manage Tables</h1>
    </div>
    <a href="<?= BASE_PATH ?>/tables/create" class="btn btn-success">+ Add Table</a>
</div>

<?php if (empty($tables)): ?>
    <div class="empty-state">
        <p>No tables yet. <a href="<?= BASE_PATH ?>/tables/create">Add one now</a>.</p>
    </div>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($tables as $t): ?>
            <div class="card table-card">
                <div class="table-icon">&#127918;</div>
                <h3><?= htmlspecialchars($t['name_table']) ?></h3>
                <p><strong>Capacity:</strong> <?= $t['capacity'] ?> players</p>
                <span class="badge badge-<?= $t['status_table'] === 'free' ? 'success' : 'danger' ?>">
                    <?= $t['status_table'] === 'free' ? '&#10003; Free' : '&#9679; Occupied' ?>
                </span>
                <div class="card-actions">
                    <?php if ($t['status_table'] === 'occupied'): ?>
                        <form action="<?= BASE_PATH ?>/tables/<?= $t['id_table'] ?>/free" method="POST"
                              style="display:inline"
                              onsubmit="return confirm('Mark <?= htmlspecialchars($t['name_table']) ?> as free? This will end any active session on this table.')">
                            <button type="submit" class="btn btn-small btn-success" title="Players left early — release this table">
                                &#10003; Set Free
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= BASE_PATH ?>/tables/<?= $t['id_table'] ?>/edit" class="btn btn-small btn-warning">&#9998; Edit</a>
                    <form action="<?= BASE_PATH ?>/tables/<?= $t['id_table'] ?>/delete" method="POST"
                          style="display:inline" onsubmit="return confirm('Delete table <?= htmlspecialchars($t['name_table']) ?>?')">
                        <button type="submit" class="btn btn-small btn-danger">&#128465; Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
