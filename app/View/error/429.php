<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
    <h1>Too many requests</h1>
    <p class="page-intro">Please slow down and try again in a moment.</p>
</div>
<div class="empty-state">
    <p>You've made too many requests in a short time. Please wait a few seconds and retry.</p>
    <p><a href="<?= BASE_PATH ?>/games" class="btn btn-secondary">Back to games</a></p>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
