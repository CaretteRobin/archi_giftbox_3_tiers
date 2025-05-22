<?php

namespace Gift\Appli\Console;

use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Utils\Eloquent;

Eloquent::init(__DIR__ . '../Conf/gift.db.conf.ini');

$prests = Prestation::with('categorie')->get(); // eager loading ici

foreach ($prests as $p) {
    $cat = $p->categorie;
    echo "{$p->libelle} | {$cat->libelle} | {$p->tarif} € / {$p->unite}\n";
}
