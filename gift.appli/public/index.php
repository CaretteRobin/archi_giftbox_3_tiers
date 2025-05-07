<?php
declare(strict_types=1);

use gift\appli\utils\Eloquent;
use Slim\Factory\AppFactory;

session_start();

require_once __DIR__ . '/../src/vendor/autoload.php';

Eloquent::init(__DIR__ . '/../src/conf/conf.ini');

$app = AppFactory::create();
$app = (require __DIR__ . '/../src/conf/routes.php')($app);
$app->run();