<?php

namespace Gift\Appli\Console;

use Gift\Appli\Core\Domain\Entities\CoffretType;
use Gift\Appli\Utils\Eloquent;

Eloquent::getInstance();

$coffrets = CoffretType::all();

foreach ($coffrets as $c) {
    echo "{$c->libelle} | {$c->description}\n";
}
