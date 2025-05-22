<?php

namespace Gift\Appli\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class TestRoutesAction {
    private string $template = 'pages/test-routes.twig';

    public function __invoke(Request $request, Response $response): Response {
        $view = Twig::fromRequest($request);
        return $view->render($response, $this->template);
    }
}
