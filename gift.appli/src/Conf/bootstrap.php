<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

use Gift\Appli\Utils\Eloquent;
use Gift\Appli\WebUI\Middlewares\FlashMiddleware;
use Gift\Appli\WebUI\Middlewares\ErrorHandlerMiddleware;
use Gift\Appli\WebUI\Middlewares\AuthorizationMiddleware;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\CatalogueService;

use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\UserService;

use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\AuthService;

use Gift\Appli\Core\Application\Services\Authorization\AuthorizationServiceInterface;
use Gift\Appli\Core\Application\Services\Authorization\AuthorizationService;

// --- SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CONTAINER ---
$container = new Container();

// === SERVICE INJECTION (Usecase interfaces -> implementations) ===
$container->set(CatalogueServiceInterface::class, fn () => new CatalogueService());
$container->set(UserServiceInterface::class, fn () => new UserService());
$container->set(AuthServiceInterface::class, fn () => new AuthService());
$container->set(AuthorizationServiceInterface::class, fn () => new AuthorizationService());
$container->set(AuthorizationMiddleware::class, fn (Container $c) => new AuthorizationMiddleware(
    $c->get(AuthorizationServiceInterface::class)
));

// --- APP INITIALISATION ---
AppFactory::setContainer($container);
$app = AppFactory::create();

// --- ORM ---
Eloquent::getInstance();

// === TWIG CONFIGURATION ===
$twig = Twig::create(__DIR__ . '/../WebUI/Views', [
    'cache' => false,
    'debug' => true,
]);

$twig->addExtension(new DebugExtension());
$twig->getEnvironment()->addFunction(new TwigFunction('base_url', static function () {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    return $scriptDir === '/' ? '' : $scriptDir;
}));

// === GLOBALS TWIG ===
$twig->getEnvironment()->addGlobal('asset_base', '/public');
$twig->getEnvironment()->addGlobal('session', $_SESSION);
$twig->getEnvironment()->addGlobal('flash', $_SESSION['flash'] ?? null);

// === TWIG INJECTION ===
$container->set(Twig::class, fn () => $twig);

// === MIDDLEWARES ===
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new ErrorHandlerMiddleware($twig));
$app->add(new FlashMiddleware($twig));

// === ROUTES ===
(require_once __DIR__ . '/routes.php')($app);

// === RETURN APP ===
return $app;
