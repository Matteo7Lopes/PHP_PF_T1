<?php
$pages = json_decode($pages ?? '[]', true);
?>

<h1>Gestion des pages</h1>

<a href="/admin">Retour au dashboard</a> | <a href="/admin/pages/create">Créer une nouvelle page</a>

<hr>

<h2>Liste des pages (<?= count($pages) ?>)</h2>

<?php if (empty($pages)): ?>
    <p>Aucune page</p>
<?php else: ?>
    <?php foreach ($pages as $page): ?>
        <p>
            <strong><?= htmlspecialchars($page['title']) ?></strong><br>
            Slug : /<?= htmlspecialchars($page['slug']) ?><br>
            Statut : <?= $page['is_published'] ? 'Publié' : 'Brouillon' ?><br>
            Créé le : <?= $page['created_at'] ?><br>
            <?php if ($page['updated_at']): ?>
                Modifié le : <?= $page['updated_at'] ?><br>
            <?php endif; ?>
            <a href="/admin/pages/edit?id=<?= $page['id'] ?>">Éditer</a>
            | <a href="/<?= htmlspecialchars($page['slug']) ?>" target="_blank">Voir</a>
            | <a href="/admin/pages/delete?id=<?= $page['id'] ?>" onclick="return confirm('Supprimer cette page ?')">Supprimer</a>
        </p>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>
