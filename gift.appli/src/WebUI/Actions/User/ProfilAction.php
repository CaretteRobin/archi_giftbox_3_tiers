<?php

namespace Gift\Appli\WebUI\Actions\User;

use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfilAction
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

        if (!$user) {
            return $response->withStatus(403);
        }

        return $this->twig->render($response, 'pages/user/profil.twig', [
            'user' => $user
        ]);
    }
}
