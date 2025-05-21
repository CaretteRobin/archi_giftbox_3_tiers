<?php

namespace gift\appli\application\actions;


use Gift\Core\Application\Usecases\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ListCategoriesAction {
    private CatalogueServiceInterface $service;
    private string $template = 'pages/categories.twig';

    public function __construct(CatalogueServiceInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response {
        $categories = $this->service->getCategories();
        $view = Twig::fromRequest($request);
        return $view->render($response, $this->template, ['categories' => $categories]);
    }
}
