<?php
$pages = json_decode($pages ?? '[]', true);
?>





<hr>

<h1>Liste des pages (<?= count($pages) ?>)</h1>

<?php if (empty($pages)): ?>
    <p>Aucune page</p>
<?php else: ?>
    <?php foreach ($pages as $page): ?>
        <p>
            <strong><?= $page['title'] ?></strong><br>
            Slug : /<?= $page['slug'] ?><br>
            Statut : <?= $page['is_published'] ? 'Publié' : 'Brouillon' ?><br>
            Créé le : <?= $page['created_at'] ?><br>
            <?php if ($page['updated_at']): ?>
                Modifié le : <?= $page['updated_at'] ?><br>
            <?php endif; ?>
            <a href="/<?= $page['slug'] ?>">Voir</a>
        </p>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>
