<?php

namespace App\Controllers;

use App\Core\Render;

class Base
{
    /**
     * Page d'accueil - PROTÉGÉE (nécessite connexion)
     */
    public function index(): void
    {
        // Vérifier que l'utilisateur est connecté
        \App\Controllers\Auth::requireAuth();

        // Récupérer les infos de session
        $pseudo = $_SESSION['user_firstname'] ?? 'Invité';
        $email = $_SESSION['user_email'] ?? '';

        $render = new Render("home", "frontoffice");
        $render->assign("pseudo", $pseudo);
        $render->assign("email", $email);
        $render->render();
    }

    /**
     * Page contact - PUBLIQUE (pas besoin de connexion)
     */
    public function contact(): void
    {
        echo "<h1>Page Contact</h1>";
        echo "<p>Cette page est publique.</p>";

        if (isset($_SESSION['user_id'])) {
            echo "<p>Connecté en tant que : " . htmlspecialchars($_SESSION['user_firstname']) . "</p>";
        } else {
            echo "<p><a href='/login'>Se connecter</a></p>";
        }
    }

    /**
     * Page portfolio - PUBLIQUE
     */
    public function portfolio(): void
    {
        echo "<h1>Mon Portfolio</h1>";
        echo "<p>Bienvenue sur mon portfolio.</p>";

        if (isset($_SESSION['user_id'])) {
            echo "<p>Connecté : " . htmlspecialchars($_SESSION['user_firstname']) . "</p>";
        }
    }

    /**
     * Test de connexion à la base de données
     */
    public function testDb(): void
    {
        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();

            echo "<h1>Test de connexion</h1>";

            // Test utilisateurs
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM public."user"');
            $result = $stmt->fetch();
            echo "<p>Nombre d'utilisateurs : " . $result['count'] . "</p>";

            // Test tokens
            $stmtTokens = $pdo->query('SELECT COUNT(*) as count FROM public.user_tokens');
            $resultTokens = $stmtTokens->fetch();
            echo "<p>Nombre de tokens : " . $resultTokens['count'] . "</p>";

            // Test rôles
            $stmtRoles = $pdo->query('SELECT * FROM public.roles');
            $roles = $stmtRoles->fetchAll();
            echo "<p>Rôles disponibles :</p>";
            foreach ($roles as $role) {
                echo "- ID: {$role['id']} - {$role['name']}<br>";
            }

            // Test pages
            $stmtPages = $pdo->query('SELECT COUNT(*) as count FROM public.pages');
            $resultPages = $stmtPages->fetch();
            echo "<p>Nombre de pages : " . $resultPages['count'] . "</p>";

            echo "<hr>";
            echo "<p><strong>Toutes les tables sont OK !</strong></p>";
            echo "<p><a href='/register'>Aller à l'inscription</a></p>";

        } catch (\Exception $e) {
            echo "<h1>Erreur de connexion</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>";
            echo $e->getTraceAsString();
            echo "</pre>";
        }
    }
}