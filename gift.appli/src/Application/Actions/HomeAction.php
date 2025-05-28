<?php

namespace Gift\Appli\Application\Actions;

use Gift\Appli\Core\Application\Usecases\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeAction
{
    private CatalogueServiceInterface $catalogueService;
    private Twig $twig;

    public function __construct(CatalogueServiceInterface $catalogueService, Twig $twig)
    {
        $this->catalogueService = $catalogueService;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $categories = array_slice($this->catalogueService->getCategories(), 0, 3);
        return $this->twig->render($response, 'pages/home.twig', [
            'categories' => $categories
        ]);
    }
}
