<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use gift\appli\Models\Categorie;
use gift\appli\Models\Prestation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CategorieController {
    private Twig $twig;

    public function __construct() {
        global $twig;
        $this->twig = $twig;
    }

    /**
     * Affiche la liste des catégories
     */
    public function listCategories(Request $request, Response $response): Response {
        $categories = Categorie::all();

        return $this->twig->render($response, 'pages/categories.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Affiche les détails d'une catégorie spécifique
     */
    public function getCategorie(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];

        // Récupération de la catégorie avec Eloquent
        $categorie = Categorie::find($id);

        // Vérifier si la catégorie existe
        if (!$categorie) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html');
        }

        // Récupération des prestations de cette catégorie via la relation définie
        $prestations = $categorie->prestations;

        // Récupération de statistiques supplémentaires pour enrichir la vue
        $stats = [
            'nombrePrestations' => $categorie->nombrePrestations(),
            'prestationMoinsChere' => $categorie->prestationMoinsChere(),
            'prestationPlusChere' => $categorie->prestationPlusChere(),
            'prixMoyen' => $categorie->prixMoyen()
        ];

        return $this->twig->render($response, 'pages/categorie-details.twig', [
            'categorie' => $categorie,
            'prestations' => $prestations,
            'stats' => $stats
        ]);
    }

    /**
     * Affiche les prestations d'une catégorie spécifique
     * Utilise le template categorie-details.twig pour afficher la catégorie et ses prestations
     */
    public function getPrestationsByCategorie(Request $request, Response $response, array $args): Response
    {
        $categorieId = (int) $args['id'];

        // Vérification que la catégorie existe
        $categorie = Categorie::find($categorieId);
        if (!$categorie) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html');
        }

        // Utilisation directe de la relation pour récupérer les prestations
        $prestations = $categorie->prestations;

        // Utilisation du template categorie-details.twig qui sait déjà comment afficher une liste de prestations
        return $this->twig->render($response, 'pages/categorie-details.twig', [
            'categorie' => $categorie,
            'prestations' => $prestations,
            'stats' => [
                'nombrePrestations' => $categorie->nombrePrestations(),
                'prestationMoinsChere' => $categorie->prestationMoinsChere(),
                'prestationPlusChere' => $categorie->prestationPlusChere(),
                'prixMoyen' => $categorie->prixMoyen()
            ]
        ]);
    }

}