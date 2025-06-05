<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetPrestationsByCategorieAction
{
    private CatalogueServiceInterface $catalogueService;
    private string $template = 'pages/prestations-by-categorie.twig';

    public function __construct(CatalogueServiceInterface $catalogueService)
    {
        $this->catalogueService = $catalogueService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $categoryId = isset($args['id']) ? (int) $args['id'] : null;

        if (!$categoryId) {
            return $response->withStatus(400);
        }

        try {
            $prestations = $this->catalogueService->getPrestationsByCategory($categoryId);
            $categorie = $this->catalogueService->getCategoryById($categoryId);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        $view = Twig::fromRequest($request);
        return $view->render($response, $this->template, [
            'categorie' => $categorie,
            'prestations' => $prestations
        ]);
    }
}
