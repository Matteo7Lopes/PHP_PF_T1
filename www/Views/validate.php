<?php
$success = ($success ?? 'false') === 'true';
$message = $message ?? '';

if ($success):
?>
    <p><strong>Compte activé !</strong></p>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="/login">Se connecter</a>
<?php else: ?>
    <p><strong>Erreur</strong></p>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="/register">Créer un nouveau compte</a>
<?php endif; ?>