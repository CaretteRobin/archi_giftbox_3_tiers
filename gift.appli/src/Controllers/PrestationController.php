<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class PrestationController {
    private Twig $twig;

    public function __construct() {
        global $twig;
        $this->twig = $twig;
    }

    /**
     * Affiche les détails d'une prestation
     */
    public function getPrestation(Request $request, Response $response): Response {
        $queryParams = $request->getQueryParams();
        $id = isset($queryParams['id']) ? (int) $queryParams['id'] : null;

        // Exemple de données (à remplacer par les données réelles de votre application)
        $prestations = [
            1 => ['id' => 1, 'libelle' => 'Saut en parachute', 'description' => 'Une chute libre inoubliable suivie d\'un vol en parachute. Cette expérience unique vous permettra de découvrir des sensations extrêmes en toute sécurité, encadré par des moniteurs professionnels.', 'tarif' => 299],
            2 => ['id' => 2, 'libelle' => 'Rafting', 'description' => 'Une descente en eaux vives pour une expérience intense. Affrontez les rapides en équipe et vivez des moments d\'adrénaline pure dans un cadre naturel exceptionnel.', 'tarif' => 89],
            3 => ['id' => 3, 'libelle' => 'Massage relaxant', 'description' => 'Un massage professionnel d\'une heure pour une détente optimale. Laissez-vous aller entre les mains expertes de nos masseurs qualifiés pour un moment de relaxation intense.', 'tarif' => 75],
            4 => ['id' => 4, 'libelle' => 'Spa journée complète', 'description' => 'Accès aux installations spa et sauna pour une journée. Profitez de nos équipements haut de gamme comprenant piscine chauffée, sauna, hammam et bains à remous.', 'tarif' => 120],
            5 => ['id' => 5, 'libelle' => 'Dîner gastronomique', 'description' => 'Un menu en 5 services dans un restaurant étoilé. Dégustez des créations culinaires d\'exception préparées par un chef de renommée internationale.', 'tarif' => 150],
            6 => ['id' => 6, 'libelle' => 'Atelier cuisine', 'description' => 'Apprenez à cuisiner avec un chef renommé. Cette session interactive vous permettra d\'acquérir des techniques professionnelles et de découvrir des secrets culinaires.', 'tarif' => 95],
        ];

        // Récupérer la prestation si elle existe
        $prestation = isset($id, $prestations[$id]) ? $prestations[$id] : null;

        return $this->twig->render($response, 'pages/prestation-details.twig', [
            'prestation' => $prestation
        ]);
    }
}