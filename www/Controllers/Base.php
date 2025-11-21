<?php

namespace App\Controllers;

use App\Core\Render;

class Base
{

    public function index(): void
    {
        $lastname = "SKRZYPCZYK";


        $render = new Render("home", "frontoffice");
        $render->assign("lastname", $lastname);
        $render->assign("pseudo", "Prof");
        $render->render();
    }

    public function contact(): void
    {
        echo "Base contact";
    }


    public function portfolio(): void
    {
        echo "Base portfolio";
    }

}