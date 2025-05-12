<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/vendor/autoload.php';

/* application bootstrap */
$app = require_once __DIR__ . '/../src/conf/bootstrap.php';
$app->run();