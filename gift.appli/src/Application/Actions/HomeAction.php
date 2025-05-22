<?php

namespace Gift\Appli\Application\Actions;


use Gift\Appli\Core\Domain\Entities\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeAction
{
    private Twig $twig;

    public function __construct() {
        global $twig;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {

        // Récupérer les 3 premières catégories de la base de données
        $categories = Categorie::take(3)->get();

        // Rendre la vue home avec les catégories récupérées
        return $this->twig->render($response, 'pages/home.twig', [
            'categories' => $categories
        ]);
    }
}
