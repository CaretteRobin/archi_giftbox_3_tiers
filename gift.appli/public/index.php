<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/vendor/autoload.php';
require_once __DIR__ . '/../src/conf/bootstrap.php';

use gift\appli\Middlewares\FlashMiddleware;
use gift\appli\Middlewares\AuthMiddleware;

global $app;
global $twig;

$app->add(new FlashMiddleware($twig));
$app->add(new AuthMiddleware());

$app->run();