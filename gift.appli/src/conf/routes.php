<?php
declare(strict_types=1);

use gift\appli\Controllers\CategorieController;
use gift\appli\Controllers\HomeController;
use gift\appli\Controllers\PrestationController;
use gift\appli\Controllers\TestController;
use Slim\App;

return function (App $app): App {
    global $twig;

    // Route 1 : Affichage des catégories
    $app->get('/categories[/]', function ($request, $response) {
        $controller = new CategorieController();
        return $controller->listCategories($request, $response);
    });

    // Route 2 : Affichage d'une catégorie
    $app->get('/categorie/{id}[/]', function ($request, $response, $args) {
        $controller = new CategorieController();
        return $controller->getCategorie($request, $response, $args);
    });

    // Route 3 : Affichage d'une prestation
    $app->get('/prestation/{id}[/]', function ($request, $response, $args) {
        $controller = new PrestationController();
        return $controller->getPrestation($request, $response, $args);
    });

    $app->get('/', [HomeController::class, 'home']);

    // Route 4 : Page de test des routes
//    $app->get('/', function ($request, $response) use ($twig) {
//        $controller = new TestController($twig);
//        return $controller->testRoutes($request, $response);
//    });

    // Route 5 : Affichage des prestations d'une catégorie
    $app->get('/categorie/{id}/prestations[/]', [CategorieController::class, 'getPrestationsByCategorie']);
    return $app;
};