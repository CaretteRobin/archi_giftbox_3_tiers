<?php

namespace Gift\Appli\Middlewares;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Usecases\AuthorizationServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthorizationMiddleware implements MiddlewareInterface
{
    private AuthorizationServiceInterface $authorizationService;
    private int $operation;
    private ?string $boxIdParam;
    private string $redirectUrl;

    public function __construct(
        AuthorizationServiceInterface $authorizationService,
        int $operation,
        ?string $boxIdParam = null,
        string $redirectUrl = '/login'
    ) {
        $this->authorizationService = $authorizationService;
        $this->operation = $operation;
        $this->boxIdParam = $boxIdParam;
        $this->redirectUrl = $redirectUrl;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $boxId = null;

            // Si un paramètre de box est spécifié, l'extraire de la route
            if ($this->boxIdParam !== null) {
                $args = $request->getAttribute('routeArguments');
                $boxId = $args[$this->boxIdParam] ?? null;
            }

            // Si le boxId n'est pas dans les paramètres de route mais qu'on a besoin d'une box,
            // vérifier dans la session pour le coffret courant
            if ($boxId === null && $this->boxIdParam !== null && isset($_SESSION['current_box_id'])) {
                $boxId = $_SESSION['current_box_id'];
            }

            // Vérifier l'autorisation
            $this->authorizationService->checkAuthorization($this->operation, $boxId);

            // Si autorisé, continuer
            return $handler->handle($request);

        } catch (BoxException $e) {
            // Non autorisé, rediriger
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à effectuer cette action'
            ];

            $response = new Response();
            return $response
                ->withHeader('Location', $this->redirectUrl)
                ->withStatus(302);
        }
    }
}