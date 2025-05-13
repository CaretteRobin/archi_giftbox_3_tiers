<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use gift\appli\Models\Prestation;

class PrestationController {
    private Twig $twig;

    public function __construct() {
        global $twig;
        $this->twig = $twig;
    }

    /**
     * Affiche les détails d'une prestation
     */
    public function getPrestation(Request $request, Response $response, array $args = []): Response {
        // Vérifier si l'ID est fourni dans les arguments de la route ou dans les paramètres de requête
        $id = isset($args['id']) ? $args['id'] : null;

        if ($id === null) {
            $queryParams = $request->getQueryParams();
            $id = isset($queryParams['id']) ? $queryParams['id'] : null;
        }

        if ($id === null) {
            // Aucun ID n'a été fourni
            return $this->twig->render($response->withStatus(400), 'pages/prestation-details.twig', [
                'prestation' => null,
                'error' => 'Aucun identifiant de prestation fourni'
            ]);
        }

        // Récupération de la prestation avec Eloquent
        // Notez que la clé primaire est une chaîne (varchar) et non un entier
        $prestation = Prestation::find($id);

        // Vérifier si la prestation existe
        if (!$prestation) {
            return $this->twig->render($response->withStatus(404), 'pages/prestation-details.twig', [
                'prestation' => null,
                'error' => 'Prestation introuvable'
            ]);
        }

        // Récupération de la catégorie associée
        $categorie = $prestation->categorie;

        // Récupération des coffrets associés à cette prestation
        $coffrets = $prestation->coffretTypes;

        // Formater le tarif et l'URL de l'image
        $tarifFormate = $prestation->getTarifFormateAttribute();
        $imageUrl = $prestation->getImageUrl();

        return $this->twig->render($response, 'pages/prestation-details.twig', [
            'prestation' => $prestation,
            'categorie' => $categorie,
            'coffrets' => $coffrets,
            'tarifFormate' => $tarifFormate,
            'imageUrl' => $imageUrl
        ]);
    }
}