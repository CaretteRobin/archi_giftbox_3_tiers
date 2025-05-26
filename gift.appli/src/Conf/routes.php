<?php
declare(strict_types=1);


use Gift\Appli\Application\Actions\Auth\LogOutAction;
use Gift\Appli\Application\Actions\Auth\RegisterAction;
use Gift\Appli\Application\Actions\Auth\ShowAuthPageAction;
use Gift\Appli\Application\Actions\Auth\SignInAction;
use Gift\Appli\Application\Actions\GetCategorieAction;
use Gift\Appli\Application\Actions\GetPrestationAction;
use Gift\Appli\Application\Actions\GetPrestationByIdAction;
use Gift\Appli\Application\Actions\GetPrestationsByCategorieAction;
use Gift\Appli\Application\Actions\HomeAction;
use Gift\Appli\Application\Actions\ListCategoriesAction;
use Gift\Appli\Application\Actions\TestRoutesAction;
use Gift\Appli\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): App {

    /* -----------------------------------------------------------------
       ROUTES PUBLIQUES
       -----------------------------------------------------------------*/
    // Accueil
    $app->get('/', HomeAction::class)->setName('home');

    $app->group('/categories', function (RouteCollectorProxy $cat) {
        $cat->get('', ListCategoriesAction::class)->setName('list_categories');
        $cat->get('/{id}', GetCategorieAction::class)->setName('categorie_details');
        $cat->get('/{id}/prestations', GetPrestationsByCategorieAction::class)->setName('categorie_prestations');
    });

    $app->group('/prestations', function (RouteCollectorProxy $pre) {
        $pre->get('', GetPrestationAction::class)->setName('list_prestations');
        $pre->get('/{id}', GetPrestationByIdAction::class)->setName('prestation_details');
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