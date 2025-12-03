<?php
$errors = json_decode($errors ?? '[]', true);
$success = ($success ?? 'false') === 'true';
$token = $token ?? '';

if ($success):
?>
    <p>Mot de passe réinitialisé avec succès !</p>
    <a href="/login">Se connecter</a>
<?php else:
    if (!empty($errors)) {
        echo "<pre>";
        print_r($errors);
        echo "</pre>";
    }

    if (empty($token)):
?>
        <p>Token manquant ou invalide.</p>
        <a href="/forgot-password">Demander un nouveau lien</a>
<?php else: ?>
        <form method="post">
            <label for="password">Nouveau mot de passe :</label>
            <input type="password" id="password" name="password" required minlength="8"><br>

            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8"><br>

            <button type="submit">Réinitialiser</button>
        </form>
<?php
    endif;
endif;
?>