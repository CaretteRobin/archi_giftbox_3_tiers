<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBoxesAction
{
    private Twig $twig;
    private UserProviderInterface $userProvider;

    public function __construct(Twig $twig, UserProviderInterface $userProvider)
    {
        $this->twig = $twig;
        $this->userProvider = $userProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $user = $this->userProvider->current();
        $userId = $user ? $user->id : null;

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
