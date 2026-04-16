<section class="admin-dashboard">
    <h2>Tableau de Bord Gestionnaire ☕</h2>

    <div class="stats-bar">
        <div class="stat-item">Tables Occupées : <strong><?= $stats['occupied_tables'] ?></strong></div>
        <div class="stat-item">Sessions Actives : <strong><?= $stats['active_sessions'] ?></strong></div>
    </div>

    <div class="admin-grid">
        <div class="card active-sessions">
            <h3>🕹️ Sessions en cours</h3>
            <table>
                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Jeu</th>
                        <th>Chrono</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeSessions as $session): ?>
                    <tr>
                        <td>Table n°<?= $session->table_id ?></td>
                        <td><strong><?= $session->game_name ?></strong></td>
                        <td><span class="timer" data-start="<?= $session->start_time ?>">--:--</span></td>
                        <td>
                            <form action="/sessions/terminate" method="POST">
                                <input type="hidden" name="id" value="<?= $session->id ?>">
                                <button type="submit" class="btn-danger">Libérer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card quick-actions">
            <h3>🛠️ Actions rapides</h3>
            <div class="btn-group">
                <a href="/games/add" class="btn">➕ Ajouter un Jeu</a>
                <a href="/reservations/daily" class="btn">📋 Voir les Réservations du Jour</a>
            </div>
        </div>
    </div>
</section>

<script>
   
</script>