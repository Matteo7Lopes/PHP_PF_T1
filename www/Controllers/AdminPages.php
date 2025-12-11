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

        if ($_SERVER['REQUEST_METHOD'] == 'POST'
            && (count($_POST) == 4 || count($_POST) == 3)
            && isset($_POST["title"])
            && isset($_POST["content"])
            && isset($_POST["meta_description"])) {

            $title = strip_tags(trim($_POST['title'] ?? ''));
            $content = strip_tags(trim($_POST['content'] ?? ''));
            $metaDescription = trim($_POST['meta_description'] ?? '');
            $isPublished = isset($_POST['is_published']);
            $slug = null;
            // Validation
            if (empty($title)) {
                $errors[] = "Le titre est requis";
            } else {
                // Générer le slug
                $slug = $this->pageModel->generateSlug($title);
            }
            if (empty($errors)) {

                $data = [
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'meta_description' => $metaDescription,
                    'is_published' => $isPublished,
                    'author_id' => $_SESSION['user_id']
                ];
                $pageId = $this->pageModel->create($data);

                if ($pageId) {
                    header('Location: /admin/pages/edit?id=' . $pageId);
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création";
                    $errors[] = $isPublished;
                    $errors[] = $_POST;
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST'
            && (count($_POST) == 4 || count($_POST) == 5)
            && isset($_POST["title"])
            && isset($_POST["slug"])
            && isset($_POST["content"])
            && isset($_POST["meta_description"])) {

            // Traitement du formulaire

            $data = [
                'title' => strip_tags(trim($_POST['title'])),
                'slug' => urlencode(strtolower(trim($_POST["slug"]))),
                'content' => strip_tags(trim($_POST['content'])),
                'meta_description' => trim($_POST['meta_description']),
                'is_published' => isset($_POST['is_published'])
            ];

            // Validation
            if (empty($data['title'])) {
                $errors[] = "Le titre est requis";
            }
            if (empty($data['slug'])) {
                $errors[] = "Le slug est requis et ne doit pas être composé uniquement de caractères spéciaux";
            }
            if (!empty($this->pageModel->findAllBySlug($data['slug']))) {
                if ($data['slug'] != $page["slug"]) {
                    $errors[] = "le slug exist deja";
                }
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
