<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use gift\appli\Models\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    private Twig $twig;

    public function __construct() {
        global $twig;
        $this->twig = $twig;
    }

    public function home(Request $request, Response $response): Response
    {
        // Récupérer les 3 premières catégories de la base de données
        $categories = Categorie::take(3)->get();

        // Rendre la vue home avec les catégories récupérées
        return $this->twig->render($response, 'pages/home.twig', [
            'categories' => $categories
        ]);
    }
}