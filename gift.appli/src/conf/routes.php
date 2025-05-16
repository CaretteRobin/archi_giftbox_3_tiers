<?php
declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use gift\appli\Middlewares\AuthMiddleware;

use Gift\Application\Actions\HomeAction;
use Gift\Application\Actions\ListCategoriesAction;
use Gift\Application\Actions\GetCategorieAction;
use Gift\Application\Actions\GetPrestationsByCategorieAction;
use Gift\Application\Actions\GetPrestationAction;
use Gift\Application\Actions\GetPrestationByIdAction;
use Gift\Application\Actions\TestRoutesAction;

use Gift\Application\Actions\Auth\ShowAuthPageAction;
use Gift\Application\Actions\Auth\RegisterAction;
use Gift\Application\Actions\Auth\SignInAction;
use Gift\Application\Actions\Auth\LogoutAction;

return function (App $app): App {

    /* -----------------------------------------------------------------
       ROUTES PUBLIQUES
       -----------------------------------------------------------------*/
    // Accueil
    $app->get('/', HomeAction::class)->setName('home');

    // Catalogue des catégories
    $app->get('/categories[/]', ListCategoriesAction::class)->setName('list_categories');

    // Détail d'une catégorie + ses prestations
    $app->get('/categorie/{id}[/]', GetCategorieAction::class)->setName('get_categorie');

    // Prestations d’une catégorie
    $app->get('/categorie/{id}/prestations[/]', GetPrestationsByCategorieAction::class)->setName('get_prestations_by_categorie');

    // Détails d'une prestation
    $app->get('/prestation[/]', GetPrestationAction::class)->setName('get_prestation');
    $app->get('/prestation/{id}[/]', GetPrestationByIdAction::class)->setName('get_prestation_by_id');

    // Page de test
    $app->get('/test', TestRoutesAction::class)->setName('test_routes');

    /* -----------------------------------------------------------------
       Authentification (publiques)
       -----------------------------------------------------------------*/
    $app->get('/auth', ShowAuthPageAction::class)->setName('auth_page');
    $app->post('/register', RegisterAction::class)->setName('register');
    $app->post('/signin', SignInAction::class)->setName('signin');

    /* -----------------------------------------------------------------
       ROUTES NÉCESSITANT UNE AUTHENTIFICATION
       -----------------------------------------------------------------*/
    $app->group('', function (RouteCollectorProxy $group): void {

        // Déconnexion
        $group->get('/logout', LogoutAction::class)->setName('logout');

        // Ajoutez ici toute route supplémentaire devant être protégée :
        // $group->post('/panier/valider', ValiderPanierAction::class)->setName('valider_panier');
        // $group->get('/profil', ProfilAction::class)->setName('profil');

    })->add(AuthMiddleware::class);

    return $app;
};