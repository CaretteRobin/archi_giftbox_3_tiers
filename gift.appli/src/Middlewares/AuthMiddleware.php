<?php

namespace Gift\Appli\Middlewares;

use Gift\Appli\Core\Application\Usecases\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthServiceInterface $authService;
    private array $options;

    public function __construct(AuthServiceInterface $authService, array $options = [])
    {
        $this->authService = $authService;
        $this->options = array_merge([
            'redirect' => '/login',
            'role' => null
        ], $options);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$this->authService->isAuthenticated()) {
            // Rediriger vers la page de connexion
            $response = new Response();
            return $response
                ->withHeader('Location', $this->options['redirect'])
                ->withStatus(302);
        }

        // Vérifier le rôle si nécessaire
        if ($this->options['role'] !== null && !$this->authService->hasRole($this->options['role'])) {
            // Accès refusé
            $response = new Response();
            return $response
                ->withHeader('Location', '/access-denied')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}