<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetPrestationAction {
    private CatalogueServiceInterface $service;
    private string $template = 'pages/prestation-details.twig';

    public function __construct(CatalogueServiceInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $queryParams = $request->getQueryParams();
        $id = isset($queryParams['id']) ? (int) $queryParams['id'] : null;

        if (!$id) {
            return $response->withStatus(400); // Bad request
        }

        try {
            $prestation = $this->service->getPrestationById($id);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, $this->template, ['prestation' => $prestation]);
    }
}
