<?php

declare(strict_types=1);

use Gift\Appli\Core\Application\Usecases\CatalogueServiceInterface;
use Gift\Appli\Infrastructure\Services\CatalogueService;
use Gift\Appli\Middlewares\ErrorHandlerMiddleware;
use Gift\Appli\Utils\Eloquent;
use Slim\Factory\AppFactory;
use DI\Container;
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
$container->set(CatalogueServiceInterface::class, fn() => new CatalogueService());

// --- APP ---
AppFactory::setContainer($container);
$app = AppFactory::create();

// --- ORM INIT ---
Eloquent::getInstance();

// --- TWIG ---
$twig = Twig::create(__DIR__ . '/../Application/Views', [
    'cache' => false,
    'debug' => true,
]);

$twig->addExtension(new DebugExtension());
$twig->getEnvironment()->addFunction(new TwigFunction('base_url', function () {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    return $scriptDir === '/' ? '' : $scriptDir;
}));

// --- VARS GLOBALES TWIG ---
$twig->getEnvironment()->addGlobal('asset_base', '/public');
$twig->getEnvironment()->addGlobal('session', $_SESSION);
$twig->getEnvironment()->addGlobal('flash', $_SESSION['flash'] ?? null);

// --- TWIG MIDDLEWARE ---
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new ErrorHandlerMiddleware($twig));


// --- ROUTES ---
(require_once __DIR__ . '/routes.php')($app);


// --- RETURN ---
return $app;
