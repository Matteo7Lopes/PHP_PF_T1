<?php
$page = json_decode($page ?? '{}', true);
?>

<h1><?= htmlspecialchars($page['title'] ?? 'Page') ?></h1>

<?php if (!empty($page['meta_description'])): ?>
    <p><em><?= htmlspecialchars($page['meta_description']) ?></em></p>
    <hr>
<?php endif; ?>

<div>
    <?= nl2br(htmlspecialchars($page['content'] ?? '')) ?>
</div>

<hr>

<p><small>Publié le : <?= $page['created_at'] ?? '' ?></small></p>

<?php if (!empty($page['updated_at'])): ?>
    <p><small>Mis à jour le : <?= $page['updated_at'] ?></small></p>
<?php endif; ?>

<a href="/pages">list des pages</a><br>
<?php if ($_SESSION['user_role_id'] == 1): ?>
    <a href="/admin/pages">Gérer les pages</a><br>
<?php endif; ?>
