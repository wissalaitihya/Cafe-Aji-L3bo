<section class="player-dashboard">
    <h2>Bienvenue, <?= htmlspecialchars($userName) ?> 👋</h2>

    <div class="dashboard-grid">
        <div class="card">
            <h3>📅 Mes Réservations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Personnes</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= $res->date ?></td>
                        <td><?= $res->time ?>h</td>
                        <td><?= $res->nb_people ?></td>
                        <td><span class="badge <?= $res->status ?>"><?= $res->status ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="/reservations/create" class="btn">Réserver une nouvelle table</a>
        </div>

        <div class="card">
            <h3>🎲 Suggestions pour vous</h3>
            <p>Basé sur vos dernières parties au Aji L3bo.</p>
            </div>
    </div>
</section>