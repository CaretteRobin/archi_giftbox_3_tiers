<?php
namespace Gift\Appli\WebUI\Middlewares;

use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{

    use FlashRedirectTrait;
    private UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface

    {

        error_log('[DEBUG] SESSION : ' . print_r($_SESSION, true));
        
        if (!$this->userProvider->isLoggedIn()) {
            // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
            return ($this->redirectWithFlash(
                $request,
                'login',
                'Vous devez être connecté pour accéder à cette page.',
                'error'
            ));
        }

        // Si l'utilisateur est connecté, on continue le traitement de la requête

        $user = $this->userProvider->current();

        if (!$user) {
            // Si l'utilisateur n'est pas trouvé, on le redirige vers la page de connexion
            return ($this->redirectWithFlash(
                $request,
                'login',
                'Utilisateur introuvable.',
                'error'
            ));
        }

        // On peut ajouter l'utilisateur à la requête si nécessaire

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
