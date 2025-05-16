<?php
declare(strict_types=1);

use gift\appli\application\actions\auth\LogOutAction;
use gift\appli\application\actions\auth\RegisterAction;
use gift\appli\application\actions\auth\ShowAuthPageAction;
use gift\appli\application\actions\auth\SignInAction;
use gift\appli\application\actions\GetCategorieAction;
use gift\appli\application\actions\GetPrestationAction;
use gift\appli\application\actions\GetPrestationByIdAction;
use gift\appli\application\actions\GetPrestationsByCategorieAction;
use gift\appli\application\actions\HomeAction;
use gift\appli\application\actions\ListCategoriesAction;
use gift\appli\application\actions\TestRoutesAction;
use gift\appli\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

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
        $group->get('/logout', LogOutAction::class)->setName('logout');

        // Ajoutez ici toute route supplémentaire devant être protégée :
        // $group->post('/panier/valider', ValiderPanierAction::class)->setName('valider_panier');
        // $group->get('/profil', ProfilAction::class)->setName('profil');

    })->add(AuthMiddleware::class);

    return $app;
};