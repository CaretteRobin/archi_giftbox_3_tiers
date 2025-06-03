<?php

namespace Gift\Appli\WebUI\Actions\User;

use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Gift\Appli\Core\Application\Usecases\Services\UserService;
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
        $user = UserService::getUser() ?? null;

        if (!$user) {
            return $response->withStatus(403)->write('Access denied');
        }

        return $this->twig->render($response, 'pages/user/profil.twig', [
            'user' => $user
        ]);
    }
}
