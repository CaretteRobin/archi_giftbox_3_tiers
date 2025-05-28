<?php

namespace Gift\Appli\WebUI\Middlewares;

use Gift\Appli\Core\Domain\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Views\Twig;


class FlashMiddleware implements MiddlewareInterface
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function process(Request $request, Handler $handler): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $user = null;
        if (isset($_SESSION['user'])) {
            $user = User::find($_SESSION['user']);
        }

        // Injecter dans toutes les vues Twig
        $this->twig->getEnvironment()->addGlobal('flash', $flash);
        $this->twig->getEnvironment()->addGlobal('user', $user);

        return $handler->handle($request);
    }
}
