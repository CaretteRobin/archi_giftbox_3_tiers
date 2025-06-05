<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Usecases\Interfaces\BoxServiceInterface;
use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\WebUI\Providers\CsrfTokenProvider;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class GetPrestationByIdAction
{
    private CatalogueServiceInterface $catalogueService;
    private BoxServiceInterface $boxService;
    private AuthProviderInterface $authProvider;
    private Twig $twig;

    public function __construct(
        CatalogueServiceInterface $catalogueService,
        BoxServiceInterface $boxService,
        AuthProviderInterface $authProvider,
        Twig $twig
    ) {
        $this->catalogueService = $catalogueService;
        $this->boxService = $boxService;
        $this->authProvider = $authProvider;
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

            // Récupération de l'utilisateur connecté
            $user = $this->authProvider->getLoggedUser();
            $userBoxes = null;
            $csrf_token = null;

            // Si l'utilisateur est connecté, récupérer ses boxes
            if ($user) {
                // Récupération des boxes de l'utilisateur
                $userBoxes = Box::where('createur_id', $user->id)
                    ->where('statut', Box::STATUT_CREE)
                    ->get();
                $csrf_token = CsrfTokenProvider::generate();
            }

            return $this->twig->render($response, 'pages/prestation-details.twig', [
                'prestation' => $prestation,
                'categorie' => $categorie,
                'coffrets' => $coffrets,
                'tarifFormate' => $tarifFormate,
                'imageUrl' => $imageUrl,
                'user' => $user,
                'userBoxes' => $userBoxes,
                'csrf_token' => $csrf_token
            ]);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }
    }
}