<?php
// /gift.appli/src/WebUI/Actions/Api/ListPrestationsApiAction.php

namespace Gift\Appli\WebUI\Actions\Api;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListPrestationsApiAction extends ApiAction
{
    private CatalogueServiceInterface $service;

    public function __construct(CatalogueServiceInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        // Le CatalogueService n'a pas de méthode getPrestations()
        // Nous récupérons toutes les prestations de toutes les catégories
        $categories = $this->service->getCategories();
        $prestations = [];

        foreach ($categories as $category) {
            $catPrestations = $this->service->getPrestationsByCategory($category['id']);
            $prestations = array_merge($prestations, $catPrestations);
        }

        $result = [
            "type" => "collection",
            "count" => count($prestations),
            "prestations" => $prestations
        ];

        return $this->jsonResponse($response, $result);
    }
}