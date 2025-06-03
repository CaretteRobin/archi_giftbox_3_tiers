<?php

declare(strict_types=1);

use DI\Container;
use Gift\Appli\WebUI\Middlewares\ErrorHandlerMiddleware;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\CatalogueService;
use Gift\Appli\Core\Application\Services\Authorization\AuthorizationService;
use Gift\Appli\Core\Application\Services\Authorization\AuthorizationServiceInterface;
use Gift\Appli\WebUI\Middlewares\AuthorizationMiddleware;
use Gift\Appli\Utils\Eloquent;
use Gift\Appli\WebUI\Middlewares\FlashMiddleware;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

// --- SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CONTAINER ---
$container = new Container();

// --- SERVICE INJECTION (métier) ---
$container->set(CatalogueServiceInterface::class, fn () => new CatalogueService());

// --- AJOUT DU SERVICE D'AUTORISATION ---
$container->set(AuthorizationServiceInterface::class, fn () => new AuthorizationService());
$container->set(AuthorizationMiddleware::class, function (Container $container) {
    return new AuthorizationMiddleware(
        $container->get(AuthorizationServiceInterface::class)
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
$twig->getEnvironment()->addGlobal('session', $_SESSION);
$twig->getEnvironment()->addGlobal('flash', $_SESSION['flash'] ?? null);

// --- TWIG MIDDLEWARE ---
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new ErrorHandlerMiddleware($twig));
$app->add(new FlashMiddleware($twig));

// --- ROUTES ---
(require_once __DIR__ . '/routes.php')($app);


// --- RETURN ---
return $app;