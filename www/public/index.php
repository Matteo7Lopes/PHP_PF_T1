<?php

namespace App;

// Démarrer la session
session_start();

// Charger la configuration
require_once __DIR__ . '/../config.php';

// Charger l'autoloader Composer (pour PHPMailer)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Autoloader personnalisé
spl_autoload_register(function ($class){
    $class = str_ireplace(["\\", "App"], ["/", ".."],$class);
    if(file_exists($class.".php")){
        include $class.".php";
    }
});

// Récupérer l'URI
$requestUri = strtok($_SERVER["REQUEST_URI"], "?");
if(strlen($requestUri) > 1)
    $requestUri = rtrim($requestUri, "/");
$requestUri = strtolower($requestUri);

// Charger les routes
$routes = yaml_parse_file("../routes.yml");

// Vérifier si l'URI correspond à une route statique
if(!empty($routes[$requestUri])){
    // Route statique trouvée
    if(empty($routes[$requestUri]["controller"]) || empty($routes[$requestUri]["action"])){
        http_response_code(404);
        die("Aucun controller ou action pour cette uri : page 404");
    }

    $controller = $routes[$requestUri]["controller"];
    $action = $routes[$requestUri]["action"];

    if(!file_exists("../Controllers/".$controller.".php")){
        http_response_code(500);
        die("Aucun fichier controller pour cette uri");
    }

    include "../Controllers/".$controller.".php";

    $controller = "App\\Controllers\\".$controller;
    if(!class_exists($controller)){
        http_response_code(500);
        die("La classe du controller n'existe pas");
    }

    $objetController = new $controller();

    if(!method_exists($objetController, $action)){
        http_response_code(500);
        die("La methode du controller n'existe pas");
    }

    $objetController->$action();

} else {
    // Pas de route statique, chercher une page dynamique
    $slug = ltrim($requestUri, '/');

    // Si le slug est vide, rediriger vers /
    if(empty($slug)){
        http_response_code(404);
        die("Page non trouvée");
    }

    // Charger le controller de pages dynamiques
    $dynamicController = new \App\Controllers\DynamicPage();
    $dynamicController->show($slug);
}
