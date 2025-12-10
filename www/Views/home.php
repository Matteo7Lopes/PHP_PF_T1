<h2>Bienvenue <?= htmlspecialchars($pseudo) ?></h2>

<p>Email : <?= htmlspecialchars($email) ?></p>

<h3>Menu</h3>
<?php
if ($_SESSION['user_role_id'] == 1) { ?>
<a href="/admin">dashboard</a><br>
<?php } ?>
<a href="/contact">Contact</a><br>
<a href="/portfolio">Portfolio</a><br>
<a href="/pages">list des pages</a><br>
<a href="/logout">Déconnexion</a>

