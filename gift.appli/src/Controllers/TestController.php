<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TestController {
    /**
     * Affiche une page pour tester les différentes routes
     */
    public function testRoutes(Request $request, Response $response): Response {
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
                    border-radius: 4px;
                    font-size: 16px;
                    border: 1px solid gray;
                    
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
    }
}