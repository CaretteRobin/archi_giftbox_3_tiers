<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\WebUI\Providers\CsrfTokenProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ShowAuthPageAction
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $csrfToken = CsrfTokenProvider::generate();
//        $csrfToken = '';

        return $this->twig->render($response, 'pages/auth.twig', [
            'csrf_token' => $csrfToken
        ]);
    }
}
