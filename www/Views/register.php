<?php
$errors = json_decode($errors ?? '[]', true);
$success = ($success ?? 'false') === 'true';
if ($success):
?>
    <p>Inscription réussie ! Email de confirmation envoyé.</p>
    <a href="/login">Retour à la connexion</a>
<?php else:
    if (!empty($errors)) {
        echo "<pre>";
        print_r($errors);
        echo "</pre>";
    }
?>
    <form method="post">
        <label for="firstname">Prénom :</label>
        <input type="text" id="firstname" name="firstname" required><br>

        <label for="lastname">Nom :</label>
        <input type="text" id="lastname" name="lastname" required><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required minlength="8"><br>

        <label for="confirm_password">Confirmer le mot de passe :</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8"><br>

        <button type="submit">S'inscrire</button>
    </form>

    <a href="/login">Se connecter</a>
<?php endif; ?>