<?php

namespace Gift\Appli\WebUI\Actions\User;

use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfilAction
{
    private UserServiceInterface $userService;
    private Twig $twig;

    public function __construct(UserServiceInterface $userService, Twig $twig)
    {
        $this->userService = $userService;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            return $response->withStatus(403);
        }

        $user = $this->userService->getUserById($userId);

        return $this->twig->render($response, 'pages/user/profil.twig', [
            'user' => $user
        ]);
    }
}
