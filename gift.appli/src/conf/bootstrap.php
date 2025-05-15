<?php

declare(strict_types=1);

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

// --- ROUTES ---
(require_once __DIR__ . '/routes.php')($app);

// --- ERROR HANDLER ---
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (
    $request,
    $exception,
    $displayErrorDetails,
    $logErrors,
    $logErrorDetails
) use ($app, $twig) {
    $statusCode = 500;
    $message = "Une erreur s'est produite";
    $errorDetails = $exception->getTraceAsString();

    if ($exception instanceof \Slim\Exception\HttpException) {
        $statusCode = $exception->getCode();
        $message = $exception->getMessage();
    }

    if ($exception instanceof HttpNotFoundException) {
        $statusCode = 404;
        $message = "La page demandée n'existe pas";
    }

    $response = $app->getResponseFactory()->createResponse();
    return $twig->render($response->withStatus($statusCode), 'error.twig', [
        'code' => $statusCode,
        'message' => $message,
        'errorDetails' => $errorDetails,
    ]);
});

// --- RETURN ---
return $app;
