<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app): App {
    // Route 1 : Affichage des catégories
    $app->get('/categories', function (Request $request, Response $response): Response {
        $html = <<<HTML
        <h1>Liste des catégories</h1>
        <ul>
            <li><a href="/categorie/1">Catégorie 1</a></li>
            <li><a href="/categorie/2">Catégorie 2</a></li>
            <li><a href="/categorie/3">Catégorie 3</a></li>
        </ul>
        HTML;

        $response->getBody()->write($html);
        return $response;
    });

    // Route 2 : Affichage d'une catégorie
    $app->get('/categorie/{id}', function (Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $html = <<<HTML
        <h1>Détails de la catégorie</h1>
        <p>ID : $id</p>
        <p>Libellé : Catégorie $id</p>
        <p>Description : Description de la catégorie $id</p>
        HTML;

        $response->getBody()->write($html);
        return $response;
    });

    // Route 3 : Affichage d'une prestation
    $app->get('/prestation', function (Request $request, Response $response): Response {
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
    });

    return $app;
};