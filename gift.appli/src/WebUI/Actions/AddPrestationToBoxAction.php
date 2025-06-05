<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Usecases\Interfaces\BoxServiceInterface;
use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpUnauthorizedException;

class AddPrestationToBoxAction
{
    use FlashRedirectTrait;

    private BoxServiceInterface $boxService;
    private AuthProviderInterface $authProvider;

    public function __construct(BoxServiceInterface $boxService, AuthProviderInterface $authProvider)
    {
        $this->boxService = $boxService;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->authProvider->getLoggedUser();
        if (!$user) {
            throw new HttpUnauthorizedException($request, 'Vous devez être connecté pour ajouter une prestation à une box');
        }

        $data = $request->getParsedBody();
        $boxId = $data['box_id'] ?? null;
        $prestationId = $data['prestation_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$boxId || !$prestationId) {
            return $this->redirectWithFlash(
                $response,
                "/prestations/{$prestationId}",
                'Box ou prestation non spécifiée',
                'error'
            );
        }

        try {
            $box = Box::find($boxId);
            if (!$box || $box->createur_id !== $user->id) {
                throw new BoxException('Vous n\'êtes pas autorisé à modifier cette box', BoxException::UNAUTHORIZED_USER);
            }

            $updatedBox = $this->boxService->addPrestationToBox($boxId, $prestationId, (int)$quantity);

            return $this->redirectWithFlash(
                $response,
                "/prestations/{$prestationId}",
                'Prestation ajoutée à la box avec succès',
                'success'
            );

        } catch (BoxException $e) {
            return $this->redirectWithFlash(
                $response,
                "/prestations/{$prestationId}",
                'Erreur lors de l\'ajout de la prestation à la box : ' . $e->getMessage(),
                'error'
            );
        }
    }
}