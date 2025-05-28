<?php
namespace Gift\Appli\WebUI\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface

    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si l’utilisateur n’est pas connecté
        if (! isset($_SESSION['user'])) {
            $_SESSION['flash'] = 'Vous devez être connecté pour accéder à cette page.';

            return (new Response())
                ->withHeader('Location', '/auth')
                ->withStatus(302);
        }

        // Tout est OK : on laisse la requête continuer
        return $handler->handle($request);
    }
}
