<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GetPrestationByIdAction
{
    private CatalogueServiceInterface $catalogueService;
    private Twig $twig;

    public function __construct(CatalogueServiceInterface $catalogueService, Twig $twig)
    {
        $this->catalogueService = $catalogueService;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;

        if (!$id) {
            return $response->withStatus(400);
        }

        try {
            $prestation = $this->catalogueService->getPrestationById($id);
            // Récupération de la catégorie associée
            $categorie = $prestation->categorie;

            // Récupération des coffrets associés à cette prestation
            $coffrets = $prestation->coffretTypes;

            // Formater le tarif et l'URL de l'image
            $tarifFormate = $prestation->getTarifFormateAttribute();
            $imageUrl = $prestation->getImageUrl();

            return $this->twig->render($response, 'pages/prestation-details.twig', [
                'prestation' => $prestation,
                'categorie' => $categorie,
                'coffrets' => $coffrets,
                'tarifFormate' => $tarifFormate,
                'imageUrl' => $imageUrl
            ]);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}
