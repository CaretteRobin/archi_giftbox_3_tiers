<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategorieController {
    /**
     * Affiche la liste des catégories
     */
    public function listCategories(Request $request, Response $response): Response {
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
    }

    /**
     * Affiche les détails d'une catégorie spécifique
     */
    public function getCategorie(Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $html = <<<HTML
        <h1>Détails de la catégorie</h1>
        <p>ID : $id</p>
        <p>Libellé : Catégorie $id</p>
        <p>Description : Description de la catégorie $id</p>
        HTML;

        $response->getBody()->write($html);
        return $response;
    }
}