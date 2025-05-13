<?php

namespace gift\appli\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use gift\appli\Models\User;

class AuthMiddleware implements MiddlewareInterface
{
    private array $routes_publiques = [
        '/',               // Page d'accueil
        '/auth',           // Page d'authentification
        '/signin',         // Route de connexion
        '/register',       // Route d'inscription
        '/catalogue',      // Catalogue public
        '/box/catalogue',  // Catalogue des box
        '/categories',     // Liste des catégories
        '/categorie'       // Détails d'une catégorie
    ];


    public function process(Request $request, Handler $handler): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $route = $request->getUri()->getPath();

        // Vérifier si l'utilisateur est connecté
        $isAuthenticated = isset($_SESSION['user']);

        // Si la route n'est pas publique et l'utilisateur n'est pas connecté
        if (!$this->isPublicRoute($route) && !$isAuthenticated) {
            $_SESSION['flash'] = 'Vous devez être connecté pour accéder à cette page.';

            // Rediriger vers la page d'authentification
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location', '/auth')
                ->withStatus(302);
        }

        // Continuer le traitement si l'utilisateur est authentifié ou si la route est publique
        return $handler->handle($request);
    }

    private function isPublicRoute(string $route): bool
    {
        foreach ($this->routes_publiques as $public_route) {
            if ($route === $public_route || strpos($route, $public_route.'/') === 0) {
                return true;
            }
        }
        return false;
    }
}