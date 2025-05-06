<?php
declare(strict_types=1);

use gift\appli\utils\Eloquent;

session_start();

require_once __DIR__ . '/../src/vendor/autoload.php';

Eloquent::init(__DIR__ . '/../src/conf/conf.ini');

