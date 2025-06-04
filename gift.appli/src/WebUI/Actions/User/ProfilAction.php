<?php

namespace Gift\Appli\WebUI\Actions\User;

use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfilAction
{
    private Twig $twig;
    private AuthProviderInterface $authProvider;

    public function __construct(Twig $twig, AuthProviderInterface $authProvider)
    {
        $this->twig = $twig;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $user = $this->authProvider->getLoggedUser();

        if (!$user) {
            return $response->withStatus(403);
        }

        return $this->twig->render($response, 'pages/user/profil.twig', [
            'user' => $user
        ]);
    }
}
