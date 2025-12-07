<?php

namespace App\Controllers;

use App\Core\Render;
use App\Models\Page;

class AdminPages
{
    private Page $pageModel;
    
    public function __construct()
    {
        $this->pageModel = new Page();
    }
    
    /**
     * Liste de toutes les pages
     */
    public function list(): void
    {
        Auth::requireAdmin();
        
        $pages = $this->pageModel->findAll();
        
        $render = new Render("admin/pages-list", "backoffice");
        $render->assign("pages", json_encode($pages));
        $render->render();
    }
    
    /**
     * Créer une nouvelle page
     */
    public function create(): void
    {
        Auth::requireAdmin();
        
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $metaDescription = trim($_POST['meta_description'] ?? '');
            $isPublished = isset($_POST['is_published']);
            
            // Validation
            if (empty($title)) {
                $errors[] = "Le titre est requis";
            }
            
            if (empty($errors)) {
                // Générer le slug
                $slug = $this->pageModel->generateSlug($title);
                
                $pageId = $this->pageModel->create([
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'meta_description' => $metaDescription,
                    'is_published' => $isPublished,
                    'author_id' => $_SESSION['user_id']
                ]);
                
                if ($pageId) {
                    header('Location: /admin/pages/edit?id=' . $pageId);
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création";
                }
            }
        }
        
        $render = new Render("admin/pages-create", "backoffice");
        $render->assign("errors", json_encode($errors));
        $render->render();
    }
    
    /**
     * Éditer une page
     */
    public function edit(): void
    {
        Auth::requireAdmin();
        
        $pageId = $_GET['id'] ?? null;
        $errors = [];
        $success = false;
        
        if (!$pageId) {
            die("ID page manquant");
        }
        
        $page = $this->pageModel->findById($pageId);
        
        if (!$page) {
            die("Page introuvable");
        }
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'content' => trim($_POST['content'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
                'is_published' => isset($_POST['is_published'])
            ];
            
            // Validation
            if (empty($data['title'])) {
                $errors[] = "Le titre est requis";
            }
            if (empty($data['slug'])) {
                $errors[] = "Le slug est requis";
            }
            
            // Mise à jour
            if (empty($errors)) {
                if ($this->pageModel->update($pageId, $data)) {
                    $success = true;
                    $page = $this->pageModel->findById($pageId); // Recharger
                } else {
                    $errors[] = "Erreur lors de la mise à jour";
                }
            }
        }
        
        $render = new Render("admin/pages-edit", "backoffice");
        $render->assign("page", json_encode($page));
        $render->assign("errors", json_encode($errors));
        $render->assign("success", $success ? "true" : "false");
        $render->render();
    }
    
    /**
     * Supprimer une page
     */
    public function delete(): void
    {
        Auth::requireAdmin();
        
        $pageId = $_GET['id'] ?? null;
        
        if (!$pageId) {
            die("ID page manquant");
        }
        
        if ($this->pageModel->delete($pageId)) {
            header('Location: /admin/pages');
            exit;
        } else {
            die("Erreur lors de la suppression");
        }
    }
}
