<?php

namespace Gift\Appli\Console;


use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Utils\Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

Eloquent::getInstance();

$id = $argv[1] ?? '';
try {
    $p = Prestation::findOrFail($id);
    echo "{$p->libelle} - {$p->description} - {$p->tarif} €\n";
} catch (ModelNotFoundException $e) {
    echo "Aucune prestation trouvée pour l'ID '$id'.\n";
}
