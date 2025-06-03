<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Usecases\Services\BoxService;
use Gift\Appli\Core\Application\Usecases\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CreateBoxAction
{
    private Twig $twig;
    private BoxService $boxService;

    public function __construct(Twig $twig, BoxService $boxService)
    {
        $this->twig = $twig;
        $this->boxService = $boxService;
    }

    // Affiche le formulaire de création
    public function showForm(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/box-create.twig');
    }

    // Traite le formulaire de création
    public function handleForm(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $userId = UserService::getUser()['id'] ?? null;

        if (!$userId) {
            return $response->withHeader('Location', '/auth')->withStatus(302);
        }

        try {
            // Récupérer les données du formulaire
            $name = $data['libelle'] ?? '';
            $description = $data['description'] ?? '';
            $isGift = isset($data['kdo']) && $data['kdo'] === '1';
            $giftMessage = $data['message_kdo'] ?? null;

            // Créer la box
            $box = $this->boxService->createBox(
                $name,
                $description,
                $isGift,
                $giftMessage,
                $userId
            );

            // Rediriger vers la page de détails de la box
            // Créer un message flash

            return $response->withHeader('Location', "/boxes/{$box->id}")->withStatus(302);

        } catch (BoxException $e) {
            // En cas d'erreur, rediriger vers le formulaire avec un message d'erreur
            $_SESSION['error'] = $e->getMessage();
            return $response->withHeader('Location', '/boxes/create')->withStatus(302);
        }
    }
}