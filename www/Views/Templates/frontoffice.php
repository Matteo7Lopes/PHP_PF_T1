<!DOCTYPE html>
<html>
    <head>
        <title>Frontoffice</title>
    </head>
    <body>
        <h1>MINI WORDPRESS</h1>

        <?php include $this->viewPath;?>

        <footer>

        <?php if (strtok($_SERVER["REQUEST_URI"], "?") != "/") { ?>

        <a href="/">Accueil</a><br>

        <?php } ?>

            <marquee>mini WordPress</marquee>
        </footer>
    </body>
</html>