<?php

declare(strict_types=1);

use gift\appli\Middlewares\ErrorHandlerMiddleware;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

use gift\appli\utils\Eloquent;
use gift\core\application\usecases\CatalogueServiceInterface;
use gift\infrastructure\services\CatalogueService;

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
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// --- TWIG ---
$twig = Twig::create(__DIR__ . '/../application/views', [
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
$twig->getEnvironment()->addGlobal('nav_items', [
    ['route' => 'list_categories', 'label' => 'Catégories'],
    ['route' => 'test_routes', 'label' => 'Test'],
]);

// --- TWIG MIDDLEWARE ---
$app->add(TwigMiddleware::create($app, $twig));
$app->add(new ErrorHandlerMiddleware($twig));


// --- ROUTES ---
(require_once __DIR__ . '/routes.php')($app);


// --- RETURN ---
return $app;
