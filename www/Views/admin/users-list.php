<?php
$users = json_decode($users ?? '[]', true);
?>

<h1>Gestion des utilisateurs</h1>

<a href="/admin">Retour au dashboard</a>

<hr>

<h2>Liste des utilisateurs (<?= count($users) ?>)</h2>

<?php if (empty($users)): ?>
    <p>Aucun utilisateur</p>
<?php else: ?>
    <?php foreach ($users as $user): ?>
        <p>
            <strong><?= htmlspecialchars($user['firstname']) ?> <?= htmlspecialchars($user['lastname']) ?></strong><br>
            Email : <?= htmlspecialchars($user['email']) ?><br>
            Rôle : <?= $user['role_id'] == 1 ? 'Admin' : 'User' ?><br>
            Statut : <?= $user['is_active'] ? 'Actif' : 'Inactif' ?><br>
            Créé le : <?= $user['date_created'] ?><br>
            <a href="/admin/users/edit?id=<?= $user['id'] ?>">Éditer</a>
            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                | <a href="/admin/users/delete?id=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
            <?php endif; ?>
        </p>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>
