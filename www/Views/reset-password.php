<?php
$errors = json_decode($errors ?? '[]', true);
$success = ($success ?? 'false') == 'true';
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
            <label for="pwd">Nouveau mot de passe :</label>
            <input type="password" id="pwd" name="pwd" required minlength="8"><br>

            <label for="pwdConfirm">Confirmer le mot de passe :</label>
            <input type="password" id="pwdConfirm" name="pwdConfirm" required minlength="8"><br>

            <button type="submit">Réinitialiser</button>
        </form>
<?php
    endif;
endif;
?>