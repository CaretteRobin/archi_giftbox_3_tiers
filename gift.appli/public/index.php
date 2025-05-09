<?php
declare(strict_types=1);

use gift\appli\utils\Eloquent;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

session_start();

require_once __DIR__ . '/../src/vendor/autoload.php';

Eloquent::init(__DIR__ . '/../src/conf/conf.ini');

// Configuration de Twig avec des fonctions personnalisées
$twig = Twig::create(__DIR__ . '/../src/Views/templates', [
    'cache' => false,
    'debug' => true,
]);

// Ajout de fonctions personnalisées à Twig
$twig->addExtension(new \Twig\Extension\DebugExtension());
$twig->getEnvironment()->addFunction(new \Twig\TwigFunction('base_url', function () {
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

$app = (require __DIR__ . '/../src/conf/routes.php')($app);
$app->run();