<?php
$user = json_decode($user ?? '{}', true);
$errors = json_decode($errors ?? '[]', true);
$success = ($success ?? 'false') == 'true';

if ($success) {
    echo "<p>Utilisateur mis à jour avec succès !</p>";
}

if (!empty($errors)) {
    echo "<pre>";
    print_r($errors);
    echo "</pre>";
}
?>

<h1>Éditer l'utilisateur</h1>

<a href="/admin/users">Retour à la liste</a>

<hr>

<form method="post">
    <label for="firstname">Prénom :</label>
    <input type="text" id="firstname" name="firstname" required value="<?= $user['firstname'] ?? '' ?>"><br>

    <label for="lastname">Nom :</label>
    <input type="text" id="lastname" name="lastname" required value="<?= $user['lastname'] ?? '' ?>"><br>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required value="<?= $user['email'] ?? '' ?>"><br>

    <label for="role_id">Rôle :</label>
    <select id="role_id" name="role_id">
        <option value="2" <?= ($user['role_id']) == 2 ? 'selected' : '' ?>>User</option>
        <option value="1" <?= ($user['role_id']) == 1 ? 'selected' : '' ?>>Admin</option>
    </select><br>
    <?php if ($user['role_id'] == 2):{ ?>
        <label>
            <input type="checkbox" name="is_active" <?= ($user['is_active'] ?? false) ? 'checked' : '' ?>>
            Compte actif
        </label><br>
    <?php }endif ?>


    <button type="submit">Mettre à jour</button>
</form>
