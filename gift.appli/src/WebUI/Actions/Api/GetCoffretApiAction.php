<?php
// /gift.appli/src/WebUI/Actions/Api/GetCoffretApiAction.php

namespace Gift\Appli\WebUI\Actions\Api;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCoffretApiAction extends ApiAction
{
    private CatalogueServiceInterface $service;

    public function __construct(CatalogueServiceInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $coffretId = (int) $args['id'];

        try {
            $coffret = $this->service->getCoffretById($coffretId);

            $result = [
                "type" => "resource",
                "coffret" => $coffret
            ];

            return $this->jsonResponse($response, $result);
        } catch (\Throwable $e) {
            return $this->jsonResponse($response, [
                "error" => $e->getMessage()
            ], 404);
        }
    }
}