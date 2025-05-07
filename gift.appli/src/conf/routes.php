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

    // Route 4 : Page de test des routes
    $app->get('/', function (Request $request, Response $response): Response {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Test des routes</title>
            <style>
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body {
                    font-family: Arial, sans-serif;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                }
                h1 {
                    color: #333;
                }
                .route-group {
                    margin-bottom: 30px;
                    border: 1px solid #ddd;
                    padding: 15px;
                    border-radius: 5px;
                }
                .btn {
                font-size: 16px;
                    border: unset;
                    display: inline-block;
                    padding: 8px 16px;
                    margin: 5px;
                    background-color: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                }
                .btn:hover {
                    background-color: #45a049;
                }
                form {
                    margin-top: 10px;
                }
                input {
                    padding: 8px;
                    margin-right: 5px;
                }
            </style>
        </head>
        <body>
            <h1>Test des routes</h1>
            
            <div class="route-group">
                <h2>Route 1 : Affichage des catégories</h2>
                <p>Route: <code>/categories</code></p>
                <p>Méthode: GET</p>
                <a href="/categories" class="btn">Tester</a>
            </div>
            
            <div class="route-group">
                <h2>Route 2 : Affichage d'une catégorie</h2>
                <p>Route: <code>/categorie/{id}</code></p>
                <p>Méthode: GET</p>
                <a href="/categorie/1" class="btn">Tester avec ID=1</a>
                <a href="/categorie/2" class="btn">Tester avec ID=2</a>
                <a href="/categorie/3" class="btn">Tester avec ID=3</a>
                <form action="" onsubmit="window.location.href='/categorie/' + document.getElementById('cat-id').value; return false;">
                    <input type="number" id="cat-id" placeholder="ID de catégorie" min="1">
                    <button type="submit" class="btn">Tester</button>
                </form>
            </div>
            
            <div class="route-group">
                <h2>Route 3 : Affichage d'une prestation</h2>
                <p>Route: <code>/prestation?id={id}</code></p>
                <p>Méthode: GET</p>
                <a href="/prestation?id=1" class="btn">Tester avec ID=1</a>
                <a href="/prestation?id=2" class="btn">Tester avec ID=2</a>
                <a href="/prestation" class="btn">Tester sans ID</a>
                <form action="/prestation" method="get">
                    <input type="number" name="id" placeholder="ID de prestation" min="1">
                    <button type="submit" class="btn">Tester</button>
                </form>
            </div>
        </body>
        </html>
        HTML;

        $response->getBody()->write($html);
        return $response;
    });


    return $app;
};