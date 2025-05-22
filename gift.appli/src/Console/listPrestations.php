<?php
namespace Gift\Appli\Console;

use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Utils\Eloquent;

Eloquent::init(__DIR__ . '../Conf/gift.db.conf.ini');

$prests = Prestation::all();
foreach ($prests as $p) {
    echo "{$p->libelle} | {$p->description} | {$p->tarif} € / {$p->unite}\n";
}
