<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use gift\appli\models\CoffretType;
use gift\appli\utils\Eloquent;

Eloquent::init();

$coffrets = CoffretType::all();

foreach ($coffrets as $c) {
    echo "{$c->libelle} | {$c->description}\n";
}
