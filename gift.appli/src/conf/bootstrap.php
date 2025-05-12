<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use gift\appli\utils\Eloquent;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

session_start();

// Initialisation de l'application Slim
$app = AppFactory::create();

// Initialisation de l'ORM
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// Chargement des routes
(require_once __DIR__ . '/routes.php')($app);

// Configuration de Twig avec des fonctions personnalisées
$twig = Twig::create(__DIR__ . '/../Views/templates', [
    'cache' => false,
    'debug' => true,
]);

// Ajout de fonctions personnalisées à Twig
$twig->addExtension(new DebugExtension());
$twig->getEnvironment()->addFunction(new TwigFunction('base_url', function () {
    // Obtenir l'URL de base (à adapter selon votre configuration)
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = ($scriptDir === '/') ? '' : $scriptDir;
    return $basePath;
}));

$app = AppFactory::create();

// Ajout du middleware TwigMiddleware
$app->add(TwigMiddleware::create($app, $twig));

// Configuration du gestionnaire d'erreurs
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (
    $request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails
) use ($app, $twig) {
    $statusCode = 500;
    $message = "Une erreur s'est produite";

    if ($exception instanceof \Slim\Exception\HttpException) {
        $statusCode = $exception->getCode();
        $message = $exception->getMessage();
    }

    // Pour les erreurs 404
    if ($exception instanceof \Slim\Exception\HttpNotFoundException) {
        $statusCode = 404;
        $message = "La page demandée n'existe pas";
    }

    $response = $app->getResponseFactory()->createResponse();
    return $twig->render(
        $response->withStatus($statusCode),
        'error.twig',
        [
            'code' => $statusCode,
            'message' => $message
        ]
    );
});

$app = (require __DIR__ . '/routes.php')($app);
return $app;
