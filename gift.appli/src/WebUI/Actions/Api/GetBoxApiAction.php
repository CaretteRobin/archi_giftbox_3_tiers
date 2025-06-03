<?php
// /gift.appli/src/WebUI/Actions/Api/GetBoxApiAction.php

namespace Gift\WebUI\Actions\Api;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetBoxApiAction extends ApiAction
{
    private CatalogueServiceInterface $service;

    public function __construct(CatalogueServiceInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $boxId = $args['id'];
        $box = $this->service->getBoxById($boxId);

        if (!$box) {
            return $this->jsonResponse($response, ["error" => "Box not found"], 404);
        }

        $prestations = $this->service->getBoxPrestationById($boxId);

        $result = [
            "type" => "resource",
            "box" => [
                "id" => $box->id,
                "libelle" => $box->libelle,
                "description" => $box->description,
                "message_kdo" => $box->message_kdo,
                "statut" => $box->statut,
                "prestations" => []
            ]
        ];

        foreach ($prestations as $prestation) {
            $result["box"]["prestations"][] = [
                "libelle" => $prestation->libelle,
                "description" => $prestation->description,
                "contenu" => [
                    "box_id" => $boxId,
                    "presta_id" => $prestation->id,
                    "quantite" => $prestation->quantite
                ]
            ];
        }

        return $this->jsonResponse($response, $result);
    }
}