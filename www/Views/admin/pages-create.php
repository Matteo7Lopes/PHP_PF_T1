<?php
$errors = json_decode($errors ?? '[]', true);

if (!empty($errors)) {
    echo "<pre>";
    print_r($errors);
    echo "</pre>";
}
?>

<h1>Créer une nouvelle page</h1>

<a href="/admin/pages">Retour à la liste</a>

<hr>

<form method="post">
    <label for="title">Titre :</label>
    <input type="text" id="title" name="title" required value="<?= $_POST['title'] ?? '' ?>"><br>

    <label for="content">Contenu :</label><br>
    <textarea id="content" name="content" rows="10" cols="50"><?= $_POST['content'] ?? '' ?></textarea><br>

    <label for="meta_description">Meta description (SEO) :</label>
    <input type="text" id="meta_description" name="meta_description" maxlength="158" value="<?= $_POST['meta_description'] ?? '' ?>"><br>

    <label>
        <input type="checkbox" name="is_published" <?= isset($_POST['is_published']) ? 'checked' : '' ?>>
        Publier immédiatement
    </label><br>

    <button type="submit">Créer la page</button>
</form>

<p><em>Le slug sera généré automatiquement depuis le titre</em></p>
