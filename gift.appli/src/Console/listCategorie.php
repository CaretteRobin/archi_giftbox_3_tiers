<?php

namespace Gift\Appli\Console;

use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Utils\Eloquent;

Eloquent::getInstance();

// Récupération de toutes les catégories
$categories = Categorie::all();

// Affichage des catégories
echo "=== Liste des catégories ===\n";

if ($categories->isEmpty()) {
    echo "Aucune catégorie trouvée dans la base de données.\n";
} else {
    foreach ($categories as $categorie) {
        echo "ID: " . $categorie->id . "\n";
        echo "Libellé: " . $categorie->libelle . "\n";
        echo "Description: " . $categorie->description . "\n";
        echo "------------------------\n";
    }

    echo "Total: " . $categories->count() . " catégorie(s) trouvée(s).\n";
}