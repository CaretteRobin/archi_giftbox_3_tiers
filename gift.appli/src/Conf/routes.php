<?php
declare(strict_types=1);


use Gift\Appli\WebUI\Actions\AddPrestationToBoxAction;
use Gift\Appli\WebUI\Actions\DeleteBoxAction;
use Gift\Appli\WebUI\Actions\GetBoxesAction;
use Gift\Appli\WebUI\Actions\GetBoxesByIdAction;
use Gift\Appli\WebUI\Actions\Auth\LogOutAction;
use Gift\Appli\WebUI\Actions\Auth\RegisterAction;
use Gift\Appli\WebUI\Actions\Auth\ShowAuthPageAction;
use Gift\Appli\WebUI\Actions\Auth\SignInAction;
use Gift\Appli\WebUI\Actions\CreateBoxAction;
use Gift\Appli\WebUI\Actions\GetCategorieAction;
use Gift\Appli\WebUI\Actions\GetPrestationAction;
use Gift\Appli\WebUI\Actions\GetPrestationByIdAction;
use Gift\Appli\WebUI\Actions\GetPrestationsByCategorieAction;
use Gift\Appli\WebUI\Actions\HomeAction;
use Gift\Appli\WebUI\Actions\ListCategoriesAction;
use Gift\Appli\WebUI\Actions\TestRoutesAction;
use Gift\Appli\WebUI\Actions\UpdateBoxAction;
use Gift\Appli\WebUI\Middlewares\AuthMiddleware;
use Gift\Appli\WebUI\Actions\User\ProfilAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Gift\Appli\WebUI\Actions\Api\ListCategoriesApiAction;
use Gift\Appli\WebUI\Actions\Api\ListPrestationsApiAction;
use Gift\Appli\WebUI\Actions\Api\ListPrestationsByCategorieApiAction;
use Gift\Appli\WebUI\Actions\Api\GetCoffretApiAction;



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

    $app->get('/gift/{id}', GetBoxesByIdAction::class)->setName('gift_details');

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

        $group->group('/boxes', function (RouteCollectorProxy $pre) {
            $pre->post('/add-prestation', AddPrestationToBoxAction::class)->setName('add_prestation_to_box');
            $pre->get('', GetBoxesAction::class)->setName('list_boxes');
            $pre->get('/create', [CreateBoxAction::class, 'showForm'])->setName('box_create');
            $pre->post('/create', [CreateBoxAction::class, 'handleForm'])->setName('box_create_submit');
            $pre->group('/{id}', function (RouteCollectorProxy $box) {
                $box->get('', GetBoxesByIdAction::class)->setName('box_details');
                $box->get('/edit', [UpdateBoxAction::class, 'showForm'])->setName('box_edit');
                $box->patch('/edit', [UpdateBoxAction::class, 'handleForm'])->setName('box_edit_submit');
                $box->post('/delete', DeleteBoxAction::class)->setName('box_delete');
            });
        });
        $group->get('/profil', ProfilAction::class)->setName('profil');

    })->add(AuthMiddleware::class);

// API Routes
    $app->group('/api', function (RouteCollectorProxy $api) {
        // Liste des catégories
        $api->get('/categories', \Gift\Appli\WebUI\Actions\Api\ListCategoriesApiAction::class);

        // Liste des prestations
        $api->get('/prestations', \Gift\Appli\WebUI\Actions\Api\ListPrestationsApiAction::class);

        // Liste des prestations d'une catégorie
        $api->get('/categories/{id}/prestations', \Gift\Appli\WebUI\Actions\Api\ListPrestationsByCategorieApiAction::class);

        // Détails d'un coffret
        $api->get('/coffrets/{id}', \Gift\Appli\WebUI\Actions\Api\GetCoffretApiAction::class);
    });


    return $app;
};