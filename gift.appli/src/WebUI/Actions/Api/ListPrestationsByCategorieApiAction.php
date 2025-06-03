<?php
// /gift.appli/src/WebUI/Actions/Api/ListPrestationsByCategorieApiAction.php

namespace Gift\Appli\WebUI\Actions\Api;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListPrestationsByCategorieApiAction extends ApiAction
{
    private CatalogueServiceInterface $service;

    public function __construct(CatalogueServiceInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $categorieId = (int) $args['id'];

        try {
            $categorie = $this->service->getCategoryById($categorieId);
            $prestations = $this->service->getPrestationsByCategory($categorieId);

            $result = [
                "type" => "collection",
                "count" => count($prestations),
                "categorie" => $categorie,
                "prestations" => $prestations
            ];

            return $this->jsonResponse($response, $result);
        } catch (\Throwable $e) {
            return $this->jsonResponse($response, [
                "error" => $e->getMessage()
            ], 404);
        }
    }
}