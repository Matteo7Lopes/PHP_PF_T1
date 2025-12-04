<?php
$stats = json_decode($stats ?? '{}', true);
$latest_users = json_decode($latest_users ?? '[]', true);
?>

<h1>Dashboard Admin</h1>

<h2>Statistiques</h2>
<p>Utilisateurs totaux : <?= $stats['total_users'] ?? 0 ?></p>
<p>Utilisateurs actifs : <?= $stats['active_users'] ?? 0 ?></p>
<p>Pages totales : <?= $stats['total_pages'] ?? 0 ?></p>
<p>Pages publiées : <?= $stats['published_pages'] ?? 0 ?></p>

<hr>

<h2>Navigation</h2>
<a href="/">Retour au site</a><br>
<a href="/logout">Déconnexion</a>

<hr>

<h2>Derniers utilisateurs inscrits</h2>
<?php if (empty($latest_users)): ?>
    <p>Aucun utilisateur</p>
<?php else: ?>
    <?php foreach ($latest_users as $user): ?>
        <p>
            <?= htmlspecialchars($user['firstname']) ?>
            <?= htmlspecialchars($user['lastname']) ?>
            (<?= htmlspecialchars($user['email']) ?>)
            - <?= $user['date_created'] ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>