<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetCategorieAction {
    private CatalogueServiceInterface $service;
    private string $template = 'pages/categorie-details.twig';

    public function __construct(CatalogueServiceInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];

        try {
            $categorie = $this->service->getCategoryById($id);
            $prestations = $this->service->getPrestationsByCategory($id);
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
