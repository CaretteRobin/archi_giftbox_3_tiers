<?php
namespace Gift\Appli\Console;

use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Utils\Eloquent;

Eloquent::getInstance();

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
