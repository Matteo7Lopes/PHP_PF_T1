<?php

namespace App\Controllers;

use App\Core\Render;
use App\Models\Page;

class Pages
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
        Auth::requireAuth();

        $pages = $this->pageModel->findAll();

        $render = new Render("pages-list", "frontoffice");
        $render->assign("pages", json_encode($pages));
        $render->render();
    }

}
