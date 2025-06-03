<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Services\BoxService;
use Gift\Appli\Core\Application\Usecases\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

class DeleteBoxAction
{
    private BoxService $boxService;

    public function __construct(BoxService $boxService)
    {
        $this->boxService = $boxService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $boxId = $args['id'] ?? null;
        $userId = UserService::getUser()['id'] ?? null;

        if (!$boxId || !$userId) {
            $this->setFlashMessage('Données invalides pour la suppression');
            return $this->redirect($response, '/boxes');
        }

        try {
            // Vérifier que l'utilisateur est le propriétaire de la box
            $box = $this->boxService->getBoxDetails($boxId);

            if ($box['createur']['id'] !== $userId) {
                $this->setFlashMessage('Vous n\'êtes pas autorisé à supprimer ce coffret');
                return $this->redirect($response, '/boxes');
            }

            // Supprimer la box
            $success = $this->boxService->deleteBox($boxId, $userId);

            if ($success) {
                $this->setFlashMessage('Coffret supprimé avec succès');
            } else {
                $this->setFlashMessage('Erreur lors de la suppression du coffret');
            }

            return $this->redirect($response, '/boxes');

        } catch (EntityNotFoundException $e) {
            $this->setFlashMessage('Coffret introuvable');
            return $this->redirect($response, '/boxes');
        } catch (Throwable $e) {
            $this->setFlashMessage('Une erreur est survenue lors de la suppression du coffret');
            return $this->redirect($response, '/boxes');
        }
    }

    private function setFlashMessage(string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = $message;
    }


    private function redirect(Response $response, string $url): Response
    {
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}

