<?php

declare(strict_types=1);

use DI\Container;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthnServiceInterface;
use Gift\Appli\Core\Application\Usecases\Interfaces\BoxServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\AuthnService;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthzServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\BoxService;
use Gift\Appli\WebUI\Middlewares\AuthzMiddleware;
use Gift\Appli\WebUI\Middlewares\CsrfMiddleware;
use Gift\Appli\WebUI\Middlewares\ErrorHandlerMiddleware;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\CatalogueService;
use Gift\Appli\Core\Application\Usecases\Services\AuthzService;
use Gift\Appli\Utils\Eloquent;
use Gift\Appli\WebUI\Middlewares\FlashMessageMiddleware;
use Gift\Appli\WebUI\Providers\AuthProvider;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;
use Gift\Appli\WebUI\Actions\Api\ListCategoriesApiAction;
use Gift\Appli\WebUI\Actions\Api\ListPrestationsApiAction;
use Gift\Appli\WebUI\Actions\Api\ListPrestationsByCategorieApiAction;
use Gift\Appli\WebUI\Actions\Api\GetCoffretApiAction;
use Gift\Appli\WebUI\Actions\GetPrestationByIdAction;

// --- SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CONTAINER ---
$container = new Container();

// --- SERVICE INJECTION (métier) ---
$container->set(CatalogueServiceInterface::class, fn () => new CatalogueService());
$container->set(AuthnServiceInterface::class, fn () => new AuthnService());
$container->set(AuthProviderInterface::class, function (Container $container) {
    return new AuthProvider(
        $container->get(AuthnServiceInterface::class)
    );
}, true);
$container->set(BoxServiceInterface::class, fn () => new BoxService());


// --- AJOUT DU SERVICE D'AUTORISATION ---
$container->set(AuthzServiceInterface::class, fn () => new AuthzService());
$container->set(AuthzMiddleware::class, function (Container $container) {
    return new AuthzMiddleware(
        $container->get(AuthzServiceInterface::class)
    );
});

// --- APP ---
AppFactory::setContainer($container);
$app = AppFactory::create();

// --- ORM INIT ---
Eloquent::getInstance();

// --- TWIG ---
$twig = Twig::create(__DIR__ . '/../WebUI/Views', [
    'cache' => false,
    'debug' => true,
]);

$twig->addExtension(new DebugExtension());
$twig->getEnvironment()->addFunction(new TwigFunction('base_url', static function () {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    return $scriptDir === '/' ? '' : $scriptDir;
}));

// --- TWIG INJECTION ---
$container->set(Twig::class, fn () => $twig);

// --- VARS GLOBALES TWIG ---
$twig->getEnvironment()->addGlobal('asset_base', '/public');

// --- TWIG MIDDLEWARE ---
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new ErrorHandlerMiddleware($twig));
$app->add(new CsrfMiddleware($twig));
$app->add(new FlashMessageMiddleware($twig));

// --- ROUTES ---
(require_once __DIR__ . '/routes.php')($app);


// --- API ACTIONS ---
$container->set(ListCategoriesApiAction::class, function (Container $container) {
    return new ListCategoriesApiAction(
        $container->get(CatalogueServiceInterface::class)
    );
});

$container->set(ListPrestationsApiAction::class, function (Container $container) {
    return new ListPrestationsApiAction(
        $container->get(CatalogueServiceInterface::class)
    );
});

$container->set(ListPrestationsByCategorieApiAction::class, function (Container $container) {
    return new ListPrestationsByCategorieApiAction(
        $container->get(CatalogueServiceInterface::class)
    );
});

$container->set(GetCoffretApiAction::class, function (Container $container) {
    return new GetCoffretApiAction(
        $container->get(CatalogueServiceInterface::class)
    );
});


// --- RETURN ---
return $app;
