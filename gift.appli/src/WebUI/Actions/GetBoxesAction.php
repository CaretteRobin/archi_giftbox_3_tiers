<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Usecases\Services\UserService;
use Gift\Appli\Core\Domain\Entities\Box;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBoxesAction
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = UserService::getUser()['id'] ?? null;

        // Récupérer toutes les boxes de l'utilisateur connecté
        $boxes = Box::where('createur_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Rendu de la vue
        return $this->twig->render($response, 'pages/boxes.twig', [
            'boxes' => $boxes
        ]);
    }
}