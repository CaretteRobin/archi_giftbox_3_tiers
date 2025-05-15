<?php
declare(strict_types=1);

use Slim\App;

use Gift\Application\Actions\ListCategoriesAction;
use Gift\Application\Actions\GetCategorieAction;
use Gift\Application\Actions\GetPrestationAction;
use Gift\Application\Actions\TestRoutesAction;
use Gift\Application\Actions\GetPrestationByIdAction;
use Gift\Application\Actions\GetPrestationsByCategorieAction;
use Gift\Application\Actions\HomeAction;
use Gift\Application\Actions\Auth\ShowAuthPageAction;
use Gift\Application\Actions\Auth\RegisterAction;
use Gift\Application\Actions\Auth\SignInAction;
use Gift\Application\Actions\Auth\LogoutAction;

return function (App $app): App {

    // Page d'accueil
    $app->get('/', HomeAction::class)->setName('home');

    // Liste des catégories
    $app->get('/categories[/]', ListCategoriesAction::class)->setName('list_categories');

    // Détails d'une catégorie + ses prestations
    $app->get('/categorie/{id}[/]', GetCategorieAction::class)->setName('get_categorie');

    // Prestations d'une catégorie
    $app->get('/categorie/{id}/prestations[/]', GetPrestationsByCategorieAction::class)->setName('get_prestations_by_categorie');

    // Détail d'une prestation (query param ou id direct selon design choisi)
    $app->get('/prestation[/]', GetPrestationAction::class)->setName('get_prestation');
    $app->get('/prestation/{id}[/]', GetPrestationByIdAction::class)->setName('get_prestation_by_id');

    // Page de test
    $app->get('/test', TestRoutesAction::class)->setName('test_routes');

    // Auth - Page de connexion
    $app->get('/auth', ShowAuthPageAction::class)->setName('auth_page');

    // Auth - Inscription
    $app->post('/register', RegisterAction::class)->setName('register');

    // Auth - Connexion
    $app->post('/signin', SignInAction::class)->setName('signin');

    // Déconnexion
    $app->get('/logout', LogoutAction::class)->setName('logout');

    return $app;
};
