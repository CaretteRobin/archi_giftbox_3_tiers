<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

class RegisterAction
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

        if ($this->authService->isEmailTaken($email)) {
            $this->setFlashMessage('Cet email est déjà utilisé.');
            return $this->redirect($response, '/auth');
        }

        try {
            $userId = $this->authService->register($email, $password);
            $user = $this->userService->getUserById($userId);
            $this->userService->storeUserInSession($user);
            $this->setFlashMessage('Inscription réussie ! Bienvenue.');
            return $this->redirect($response, '/');
        } catch (Throwable $e) {
            $this->setFlashMessage('Une erreur est survenue lors de l’inscription.');
            return $this->redirect($response, '/auth');
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