<?php
namespace App\Controllers;

use App\Core\Render;

class Auth
{
    public function login(): void
    {

        $render = new Render("login", "backoffice");
        $render->render();
    }

    public function register(): void
    {
        echo "Auth register";
    }

    public function logout(): void
    {
        echo "Auth logout";
    }

}