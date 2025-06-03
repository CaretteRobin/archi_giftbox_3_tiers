<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Services\BoxService;
use Gift\Appli\Core\Application\Usecases\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Throwable;

class UpdateBoxAction
{
    private Twig $twig;
    private BoxService $boxService;

    public function __construct(Twig $twig, BoxService $boxService)
    {
        $this->twig = $twig;
        $this->boxService = $boxService;
    }

    // Affiche le formulaire de modification avec les données actuelles
    public function showForm(Request $request, Response $response, array $args): Response
    {
        $boxId = $args['id'] ?? null;
        $userId = UserService::getUser()['id'] ?? null;

        if (!$boxId) {
            $this->setFlashMessage('Identifiant de coffret invalide');
            return $this->redirect($response, '/boxes');
        }

        try {
            // Récupérer les données de la box
            $box = $this->boxService->getBoxDetails($boxId);

            // Vérifier que l'utilisateur est le propriétaire de la box
            if ($box['createur']['id'] !== $userId) {
                $this->setFlashMessage('Vous n\'êtes pas autorisé à modifier ce coffret');
                return $this->redirect($response, '/boxes');
            }

            // Passer les données à la vue
            return $this->twig->render($response, 'pages/box-edit.twig', [
                'box' => $box
            ]);

        } catch (EntityNotFoundException $e) {
            $this->setFlashMessage('Coffret introuvable');
            return $this->redirect($response, '/boxes');
        } catch (Throwable $e) {
            $this->setFlashMessage('Une erreur est survenue lors de la récupération du coffret');
            return $this->redirect($response, '/boxes');
        }
    }

    // Traite le formulaire de modification
    public function handleForm(Request $request, Response $response, array $args): Response
    {
        $boxId = $args['id'] ?? null;
        $userId = UserService::getUser()['id'] ?? null;
        $data = $request->getParsedBody();

        if (!$boxId || !$userId) {
            $this->setFlashMessage('Données invalides pour la modification');
            return $this->redirect($response, '/boxes');
        }

        try {
            // Vérifier que l'utilisateur est le propriétaire de la box
            $box = $this->boxService->getBoxDetails($boxId);

            if ($box['createur']['id'] !== $userId) {
                $this->setFlashMessage('Vous n\'êtes pas autorisé à modifier ce coffret');
                return $this->redirect($response, '/boxes');
            }

            // Préparer les données à mettre à jour
            $updateData = [
                'libelle' => $data['libelle'] ?? $box['libelle'],
                'description' => $data['description'] ?? $box['description'],
                'kdo' => isset($data['kdo']) && $data['kdo'] === '1' ? 1 : 0,
            ];

            // Si c'est un cadeau, mettre à jour le message
            if ($updateData['kdo'] === 1) {
                $updateData['message_kdo'] = $data['message_kdo'] ?? $box['message_kdo'];
            }

            // Mettre à jour la box
            $success = $this->boxService->updateBox($boxId, $userId, $updateData);

            if ($success) {
                $this->setFlashMessage('Coffret mis à jour avec succès');
                return $this->redirect($response, '/boxes/' . $boxId);
            } else {
                $this->setFlashMessage('Erreur lors de la mise à jour du coffret');
                return $this->redirect($response, '/boxes/' . $boxId . '/edit');
            }

        } catch (EntityNotFoundException $e) {
            $this->setFlashMessage('Coffret introuvable');
            return $this->redirect($response, '/boxes');
        } catch (BoxException $e) {
            $this->setFlashMessage($e->getMessage());
            return $this->redirect($response, '/boxes/' . $boxId . '/edit');
        } catch (Throwable $e) {
            $this->setFlashMessage('Une erreur est survenue lors de la mise à jour du coffret');
            return $this->redirect($response, '/boxes/' . $boxId . '/edit');
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