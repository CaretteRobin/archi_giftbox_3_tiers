<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/vendor/autoload.php';

/* application bootstrap */
$app = require_once __DIR__ . '/../src/conf/bootstrap.php';

use gift\appli\Middlewares\FlashMiddleware;
use gift\appli\Middlewares\AuthMiddleware;


global $twig;

// Ajout du middleware FlashMiddleware
// Slim 4 ne fournit pas de container par défaut, on accède directement au $twig
$app->add(new FlashMiddleware($twig));

// Ajout du middleware d'authentification
$app->add(new AuthMiddleware());

$app->run();