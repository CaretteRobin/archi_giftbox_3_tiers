<?php

namespace Gift\Appli\Console;

use Gift\Appli\Core\Domain\Entities\CoffretType;
use Gift\Appli\Utils\Eloquent;

Eloquent::getInstance();

$coffrets = CoffretType::with('prestations')->get();

foreach ($coffrets as $coffret) {
    echo "Coffret : {$coffret->libelle}\n";
    echo str_repeat('-', 40) . "\n";
    foreach ($coffret->prestations as $presta) {
        echo "- {$presta->libelle} | {$presta->tarif} €\n";
    }
    echo "\n";
}
