<?php
// /gift.appli/src/WebUI/Actions/Api/ListCategoriesApiAction.php

namespace Gift\Appli\WebUI\Actions\Api;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListCategoriesApiAction extends ApiAction
{
    private CatalogueServiceInterface $service;

    public function __construct(CatalogueServiceInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $categories = $this->service->getCategories();

        $result = [
            "type" => "collection",
            "count" => count($categories),
            "categories" => $categories
        ];

        return $this->jsonResponse($response, $result);
    }
}