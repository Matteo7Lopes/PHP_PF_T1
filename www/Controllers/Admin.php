<?php

namespace App\Controllers;

use App\Core\Render;
use App\Models\User;
use App\Models\Page;

class Admin
{
    /**
     * Dashboard admin - Page d'accueil du backoffice
     */
    public function dashboard(): void
    {
        // Vérifier que l'utilisateur est admin
        Auth::requireAdmin();

        try {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();

            // Statistiques
            $stats = [];

            // Nombre total d'utilisateurs
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM public."user"');
            $stats['total_users'] = $stmt->fetchColumn();

            // Nombre d'utilisateurs actifs
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM public."user" WHERE is_active = true');
            $stats['active_users'] = $stmt->fetchColumn();

            // Nombre de pages
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM public.pages');
            $stats['total_pages'] = $stmt->fetchColumn();

            // Nombre de pages publiées
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM public.pages WHERE is_published = true');
            $stats['published_pages'] = $stmt->fetchColumn();

            // Derniers utilisateurs inscrits
            $stmt = $pdo->query('SELECT firstname, lastname, email, date_created FROM public."user" ORDER BY date_created DESC LIMIT 5');
            $latest_users = $stmt->fetchAll();

            $render = new Render("admin/dashboard", "backoffice");
            $render->assign("stats", json_encode($stats));
            $render->assign("latest_users", json_encode($latest_users));
            $render->render();

        } catch (\Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
}