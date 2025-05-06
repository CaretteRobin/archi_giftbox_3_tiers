<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use gift\appli\models\Prestation;
use gift\appli\utils\Eloquent;

Eloquent::init();

$prests = Prestation::with('categorie')->get(); // eager loading ici

foreach ($prests as $p) {
    $cat = $p->categorie;
    echo "{$p->libelle} | {$cat->libelle} | {$p->tarif} € / {$p->unite}\n";
}
