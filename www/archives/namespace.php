<?php

namespace App\Auth;
class Security{
    public function login(){
        echo "Connexion";
    }
}
/* -----------------------------------------------*/
namespace App\Doors;
class Security{
    public function openTheDoor(){
        echo "Porte ouverte";
    }
}

/* -----------------------------------------------*/

namespace App;


new \App\Doors\Security();
new Doors\Security();









