<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use gift\appli\models\CoffretType;
use gift\appli\utils\Eloquent;

Eloquent::init();

$coffrets = CoffretType::with('prestations')->get();

foreach ($coffrets as $coffret) {
    echo "Coffret : {$coffret->libelle}\n";
    echo str_repeat('-', 40) . "\n";
    foreach ($coffret->prestations as $presta) {
        echo "- {$presta->libelle} | {$presta->tarif} €\n";
    }
    echo "\n";
}
