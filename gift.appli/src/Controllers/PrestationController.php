<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PrestationController {
    /**
     * Affiche les détails d'une prestation
     */
    public function getPrestation(Request $request, Response $response): Response {
        $queryParams = $request->getQueryParams();
        $id = $queryParams['id'] ?? null;

        if ($id === null) {
            $response->getBody()->write("Erreur : aucun ID de prestation fourni.");
        } else {
            $html = <<<HTML
            <h1>Détails de la prestation</h1>
            <p>ID : $id</p>
            <p>Libellé : Prestation $id</p>
            <p>Description : Description de la prestation $id</p>
            <p>Tarif : 100 €</p>
            HTML;

            $response->getBody()->write($html);
        }

        return $response;
    }
}