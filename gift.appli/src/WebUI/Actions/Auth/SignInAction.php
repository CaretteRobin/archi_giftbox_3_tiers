<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SignInAction
{
    private AuthServiceInterface $authService;
    private UserServiceInterface $userService;

    public function __construct(AuthServiceInterface $authService, UserServiceInterface $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlashMessage('Email ou mot de passe manquant.');
            return $this->redirect($response, '/auth');
        }

        try {
            $userId = $this->authService->login($email, $password);
            $user = $this->userService->getUserById($userId);
            $this->userService->storeUser($user);
            $this->setFlashMessage('Connexion réussie.');
            return $this->redirect($response, '/');
        } catch (EntityNotFoundException) {
            $this->setFlashMessage('Identifiants incorrects.');
        } catch (InternalErrorException|\Throwable $e) {
            $this->setFlashMessage('Erreur lors de la connexion.');
        }

        return $this->redirect($response, '/auth');
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