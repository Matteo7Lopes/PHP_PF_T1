<?php

namespace App\Controllers;

use App\Core\Render;
use App\Models\Page;

class DynamicPage
{
    private Page $pageModel;
    
    public function __construct()
    {
        $this->pageModel = new Page();
    }
    
    /**
     * Afficher une page dynamique par son slug
     */
    public function show(string $slug): void
    {
        // Chercher la page dans la base de données
        $page = $this->pageModel->findBySlug($slug);
        // Si la page n'existe pas ou n'est pas publiée
        if (!$page) {
            http_response_code(404);
            echo "<h1>404 - Page non trouvée</h1>";
            echo "<p>La page demandée n'existe pas.</p>";
            echo "<p><a href='/'>Retour à l'accueil</a></p>";
            return;
        }
        if($page["is_published"]){
            Auth::requireAuth();
        }else{
            Auth::requireAdmin();
        }
        // Afficher la page
        $render = new Render("dynamic-page", "frontoffice");
        $render->assign("page", json_encode($page));
        $render->render();
    }
}
