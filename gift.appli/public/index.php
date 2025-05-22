<?php
declare(strict_types=1);


use Gift\Appli\Middlewares\FlashMiddleware;

require_once __DIR__ . '/../src/vendor/autoload.php';
require_once __DIR__ . '/../src/Conf/bootstrap.php';

global $app;
global $twig;

$app->add(new FlashMiddleware($twig));

$app->run();