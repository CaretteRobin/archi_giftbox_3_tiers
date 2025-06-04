<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Usecases\Services\BoxService;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class GetBoxesByIdAction
{
    private Twig $twig;
    private BoxService $boxService;
    private UserProviderInterface $userProvider;

    public function __construct(Twig $twig, BoxService $boxService, UserProviderInterface $userProvider)
    {
        $this->twig = $twig;
        $this->boxService = $boxService;
        $this->userProvider = $userProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;

        if (!$id) {
            return $response->withStatus(404);
        }

        try {
            // Utiliser le service pour récupérer les détails de la box
            $boxDetails = $this->boxService->getBoxDetails($id);

            // Vérifier si l'utilisateur connecté est le créateur de la box
            $user = $this->userProvider->current();
            $userId = $user ? $user->id : null;
            $isOwner = $userId && $boxDetails['createur']['id'] === $userId;

            // Rendu de la vue
            return $this->twig->render($response, 'pages/box-details.twig', [
                'box' => $boxDetails,
                'isOwner' => $isOwner
            ]);

        } catch (BoxException $e) {
            // Rediriger vers la liste en cas d'erreur
            return $response->withHeader('Location', '/boxes')->withStatus(302);
        }
    }
}
