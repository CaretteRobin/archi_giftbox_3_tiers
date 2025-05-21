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

    $app->group('/categories', function (RouteCollectorProxy $cat) {
        $cat->get('', ListCategoriesAction::class)->setName("list_categories");                   // GET /categories
        $cat->get('/{id}', GetCategorieAction::class);               // GET /categories/{id}
        $cat->get('/{id}/prestations', GetPrestationsByCategorieAction::class);
    });
    $app->group('/prestations', function (RouteCollectorProxy $pre) {
        $pre->get('', GetPrestationAction::class);                   // GET /prestations
        $pre->get('/{id}', GetPrestationByIdAction::class);          // GET /prestations/{id}
    });
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