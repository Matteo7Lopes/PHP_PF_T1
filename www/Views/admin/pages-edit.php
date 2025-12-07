<?php
$page = json_decode($page ?? '{}', true);
$errors = json_decode($errors ?? '[]', true);
$success = ($success ?? 'false') === 'true';

if ($success) {
    echo "<p>✅ Page mise à jour avec succès !</p>";
}

if (!empty($errors)) {
    echo "<pre>";
    print_r($errors);
    echo "</pre>";
}
?>

<h1>Éditer la page</h1>

<a href="/admin/pages">Retour à la liste</a> | <a href="/<?= htmlspecialchars($page['slug'] ?? '') ?>" target="_blank">Voir la page</a>

<hr>

<form method="post">
    <label for="title">Titre :</label>
    <input type="text" id="title" name="title" required value="<?= htmlspecialchars($page['title'] ?? '') ?>"><br>

    <label for="slug">Slug (URL) :</label>
    <input type="text" id="slug" name="slug" required value="<?= htmlspecialchars($page['slug'] ?? '') ?>"><br>

    <label for="content">Contenu :</label><br>
    <textarea id="content" name="content" rows="10" cols="50"><?= htmlspecialchars($page['content'] ?? '') ?></textarea><br>

    <label for="meta_description">Meta description (SEO) :</label>
    <input type="text" id="meta_description" name="meta_description" maxlength="158" value="<?= htmlspecialchars($page['meta_description'] ?? '') ?>"><br>

    <label>
        <input type="checkbox" name="is_published" <?= ($page['is_published'] ?? false) ? 'checked' : '' ?>>
        Publié
    </label><br>

    <button type="submit">Mettre à jour</button>
</form>
