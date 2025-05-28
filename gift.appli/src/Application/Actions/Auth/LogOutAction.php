<?php
declare(strict_types=1);

namespace Gift\Appli\Application\Actions\Auth;

use Gift\Appli\Core\Application\Usecases\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogOutAction
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->authService->logout();
        $this->setFlashMessage('Déconnexion réussie.');
        return $this->redirect($response, '/');
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
