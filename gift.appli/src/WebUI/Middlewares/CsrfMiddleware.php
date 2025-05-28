<?php
namespace Gift\Appli\WebUI\Middlewares;

use Gift\Appli\WebUI\Providers\CsrfTokenProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class CsrfMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Vérifier seulement les requêtes POST, PUT, DELETE, PATCH
        $method = $request->getMethod();
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $data = $request->getParsedBody();
            $csrfToken = $data['csrf_token'] ?? null;

            try {
                CsrfTokenProvider::check($csrfToken);
            } catch (\Exception $e) {
                $_SESSION['flash'] = 'Erreur de sécurité : ' . $e->getMessage();
                return (new Response())
                    ->withHeader('Location', '/')
                    ->withStatus(302);
            }
        }

        return $handler->handle($request);
    }
}