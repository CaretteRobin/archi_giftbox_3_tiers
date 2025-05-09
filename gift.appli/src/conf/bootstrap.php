<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use gift\appli\utils\Eloquent;

require_once __DIR__ . '/../vendor/autoload.php';

// Initialisation de l'application Slim
$app = AppFactory::create();

// Initialisation de l'ORM
Eloquent::init(__DIR__ . '/gift.db.conf.ini');

// Chargement des routes
(require_once __DIR__ . '/routes.php')($app);

return $app;