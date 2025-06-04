<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class FlashMessageMiddleware implements MiddlewareInterface
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Récupérer les paramètres flash
        $queryParams = $request->getQueryParams();
        $flashMessage = $queryParams['flash-message'] ?? null;
        $flashType = $queryParams['flash-type'] ?? 'info';

        // Si un message flash existe, le rendre disponible dans les attributs de la requête
        if ($flashMessage) {
            $request = $request->withAttribute('flash', [
                'message' => urldecode($flashMessage),
                'type' => urldecode($flashType)
            ]);

            $this->twig->getEnvironment()->addGlobal('flash', [
                'message' => urldecode($flashMessage),
                'type' => urldecode($flashType)
            ]);
        }
        return $handler->handle($request);
    }
}