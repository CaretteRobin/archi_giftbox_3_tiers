<?php

/**
 * Script de test pour afficher la liste des catégories
 * Exécution en ligne de commande : php listCategories.php
 */

// Inclusion de l'autoloader et initialisation
require_once __DIR__ . '/../vendor/autoload.php';

// Initialiser Eloquent
use gift\appli\utils\Eloquent;
use gift\appli\models\Categorie;

// Initialisation de la connexion à la base de données
Eloquent::init(__DIR__ . '/../conf/gift.db.conf.ini');

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