<?php
namespace Gift\Appli\WebUI\Middlewares;

use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\CsrfTokenProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class CsrfMiddleware implements MiddlewareInterface
{
    use FlashRedirectTrait;

    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

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
                $response = new Response();
                return $this->redirectWithFlash(
                    $response,
                    'auth',
                    'Token CSRF invalide. Veuillez réessayer.',
                    'error'
                );
            }
        }

        return $handler->handle($request);
    }
}