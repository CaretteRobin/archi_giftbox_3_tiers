<?php
namespace Gift\Appli\WebUI\Middlewares;

use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{

    use FlashRedirectTrait;
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface

    {
        if (!$this->authProvider->isLoggedIn()) {
            // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
            $response = new Response();
            return $this->redirectWithFlash(
                $response,
                'auth_page',
                'Vous devez être connecté pour accéder à cette page.',
                'error'
            );
        }

        // Si l'utilisateur est connecté, on continue le traitement de la requête

        $user = $this->authProvider->getLoggedUser();

        if (!$user) {
            // Si l'utilisateur n'est pas trouvé, on le redirige vers la page de connexion
            $response = new Response();
            return $this->redirectWithFlash(
                $response,
                'auth_page',
                'Utilisateur introuvable.',
                'error'
            );
        }

        // On peut ajouter l'utilisateur à la requête si nécessaire

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
