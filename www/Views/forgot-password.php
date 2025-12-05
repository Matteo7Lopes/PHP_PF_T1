<?php
$errors = json_decode($errors ?? '[]', true);
$success = ($success ?? 'false') === 'true';

if ($success):
?>
    <p>vous recevrez un lien de réinitialisation.</p>
    <a href="/login">Retour à la connexion</a>
<?php else:
    if (!empty($errors)) {
        echo "<pre>";
        print_r($errors);
        echo "</pre>";
    }
?>
    <p>Entrez votre email pour recevoir un lien de réinitialisation.</p>

    <form method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br>
        <button type="submit">Envoyer le lien</button>
    </form>

    <a href="/login">Retour à la connexion</a>
<?php endif; ?>