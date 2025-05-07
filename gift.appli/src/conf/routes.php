<?php
declare(strict_types=1);

use gift\appli\Controllers\CategorieController;
use gift\appli\Controllers\PrestationController;
use gift\appli\Controllers\TestController;
use Slim\App;

return function (App $app): App {
    // Route 1 : Affichage des catégories
    $app->get('/categories', [CategorieController::class, 'listCategories']);

    // Route 2 : Affichage d'une catégorie
    $app->get('/categorie/{id}', [CategorieController::class, 'getCategorie']);

    // Route 3 : Affichage d'une prestation
    $app->get('/prestation', [PrestationController::class, 'getPrestation']);

    // Route 4 : Page de test des routes
    $app->get('/', [TestController::class, 'testRoutes']);

    return $app;
};