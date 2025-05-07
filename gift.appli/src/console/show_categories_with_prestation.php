<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use gift\appli\models\Categorie;
use gift\appli\utils\Eloquent;

Eloquent::init();

$categorie = Categorie::find(3);

if ($categorie) {
    echo "Catégorie : {$categorie->libelle}\n";
    echo str_repeat('-', 60) . "\n";

    foreach ($categorie->prestations as $p) {
        echo "{$p->libelle} | {$p->description} | {$p->tarif} € / {$p->unite}\n";
    }
} else {
    echo "Catégorie 3 non trouvée.\n";
}
