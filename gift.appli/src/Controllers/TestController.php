<?php
declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class TestController {
    private Twig $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    /**
     * Affiche une page pour tester les différentes routes
     */
    public function testRoutes(Request $request, Response $response): Response {
        return $this->twig->render($response, 'pages/test-routes.twig');
    }
}