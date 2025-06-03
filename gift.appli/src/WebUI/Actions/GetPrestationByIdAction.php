<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Usecases\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetPrestationByIdAction
{
    private CatalogueServiceInterface $catalogueService;
    private string $template = 'pages/prestation-details.twig';

    public function __construct(CatalogueServiceInterface $catalogueService)
    {
        $this->catalogueService = $catalogueService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;

        if (!$id) {
            return $response->withStatus(400);
        }

        try {
            $prestation = $this->catalogueService->getPrestationById($id);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, $this->template, ['prestation' => $prestation]);
    }
}
