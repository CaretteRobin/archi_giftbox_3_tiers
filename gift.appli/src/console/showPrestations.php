<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use gift\appli\models\Prestation;
use gift\appli\utils\Eloquent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

Eloquent::init();

$id = $argv[1] ?? '';
try {
    $p = Prestation::findOrFail($id);
    echo "{$p->libelle} - {$p->description} - {$p->tarif} €\n";
} catch (ModelNotFoundException $e) {
    echo "Aucune prestation trouvée pour l'ID '$id'.\n";
}
