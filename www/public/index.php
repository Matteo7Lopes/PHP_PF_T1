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

// Vérifier que l'uri existe dans les routes
if(empty($routes[$requestUri])){
    http_response_code(404);
    die("Aucune route pour cette uri : page 404");
}

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