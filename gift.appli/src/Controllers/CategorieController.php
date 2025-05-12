<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

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
        // Exemple de données (à remplacer par les données réelles de votre application)
        $categories = [
            ['id' => 1, 'libelle' => 'Aventure', 'description' => 'Des expériences pleines d\'adrénaline pour les amateurs de sensations fortes.'],
            ['id' => 2, 'libelle' => 'Détente', 'description' => 'Des moments de relaxation pour échapper au stress quotidien.'],
            ['id' => 3, 'libelle' => 'Gastronomie', 'description' => 'Des expériences culinaires inoubliables pour les gourmets.'],
        ];

        return $this->twig->render($response, 'pages/categories.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Affiche les détails d'une catégorie spécifique
     */
    public function getCategorie(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];

        // Exemple de données (à remplacer par les données réelles de votre application)
        $categories = [
            1 => ['id' => 1, 'libelle' => 'Aventure', 'description' => 'Des expériences pleines d\'adrénaline pour les amateurs de sensations fortes.'],
            2 => ['id' => 2, 'libelle' => 'Détente', 'description' => 'Des moments de relaxation pour échapper au stress quotidien.'],
            3 => ['id' => 3, 'libelle' => 'Gastronomie', 'description' => 'Des expériences culinaires inoubliables pour les gourmets.'],
        ];

        $prestations = [
            // Prestations pour Aventure
            1 => [
                ['id' => 1, 'libelle' => 'Saut en parachute', 'description' => 'Une chute libre inoubliable suivie d\'un vol en parachute.', 'tarif' => 299],
                ['id' => 2, 'libelle' => 'Rafting', 'description' => 'Une descente en eaux vives pour une expérience intense.', 'tarif' => 89],
            ],
            // Prestations pour Détente
            2 => [
                ['id' => 3, 'libelle' => 'Massage relaxant', 'description' => 'Un massage professionnel d\'une heure pour une détente optimale.', 'tarif' => 75],
                ['id' => 4, 'libelle' => 'Spa journée complète', 'description' => 'Accès aux installations spa et sauna pour une journée.', 'tarif' => 120],
            ],
            // Prestations pour Gastronomie
            3 => [
                ['id' => 5, 'libelle' => 'Dîner gastronomique', 'description' => 'Un menu en 5 services dans un restaurant étoilé.', 'tarif' => 150],
                ['id' => 6, 'libelle' => 'Atelier cuisine', 'description' => 'Apprenez à cuisiner avec un chef renommé.', 'tarif' => 95],
            ],
        ];

        // Vérifier si la catégorie existe
        if (!isset($categories[$id])) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html');
        }

        $categorie = $categories[$id];
        $categoriesPrestations = $prestations[$id] ?? [];

        return $this->twig->render($response, 'pages/categorie-details.twig', [
            'categorie' => $categorie,
            'prestations' => $categoriesPrestations
        ]);
    }

    /**
     * Affiche les prestations d'une catégorie spécifique
     */
    public function getPrestationsByCategorie(Request $request, Response $response, array $args): Response
    {
        $categorieId = $args['id'];
        $prestations = Prestation::where('categorie_id', $categorieId)->get();

        $html = "<h1>Prestations de la catégorie $categorieId</h1><ul>";
        foreach ($prestations as $prestation) {
            $html .= "<li>{$prestation->libelle} - {$prestation->tarif} €</li>";
        }
        $html .= "</ul>";

        $response->getBody()->write($html);
        return $response;
    }

}