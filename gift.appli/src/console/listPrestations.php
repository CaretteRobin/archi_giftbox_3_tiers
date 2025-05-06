<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use gift\appli\models\Prestation;
use gift\appli\utils\Eloquent;

Eloquent::init();

$prests = Prestation::all();
foreach ($prests as $p) {
    echo "{$p->libelle} | {$p->description} | {$p->tarif} € / {$p->unite}\n";
}
