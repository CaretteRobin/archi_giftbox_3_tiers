<?php
declare(strict_types=1);

namespace gift\appli\Application\Actions\Auth;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ShowAuthPageAction
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/auth.twig');
    }
}