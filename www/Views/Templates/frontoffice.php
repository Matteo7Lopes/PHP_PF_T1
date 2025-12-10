<!DOCTYPE html>
<html>
<head>
    <title>Frontoffice</title>
</head>
<body>
<h1>MINI WORDPRESS</h1>

<?php include $this->viewPath; ?>

<footer>

    <?php if ($_SERVER["REQUEST_URI"] == "/login") { ?>

    <?php } elseif ($_SERVER["REQUEST_URI"] == "/") { ?>

    <?php }else{?>
        <a href="/">Accueil</a><br>
    <?php } ?>

    <marquee>mini WordPress</marquee>
</footer>
</body>
</html>