<?php
$errors = json_decode($errors ?? '[]', true);
if (!empty($errors)) {
    echo "<pre>";
    print_r($errors);
    echo "</pre>";
}
?>

<form method="post">
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Mot de passe :</label>
    <input type="password" id="password" name="password" required><br>

    <button type="submit">Se connecter</button>
</form>

<a href="/forgot-password">Mot de passe oublié ?</a><br>
<a href="/register">Créer un compte</a>
<hr>
<p> Pages accessibles sans connexion :</p>

<a href="/portfolio">Portfolio</a><br>
<a href="/contact">Contact</a>