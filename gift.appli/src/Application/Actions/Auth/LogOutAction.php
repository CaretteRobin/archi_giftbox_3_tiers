<?php
declare(strict_types=1);

namespace Gift\Appli\Application\Actions\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogOutAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->logoutUser();
        $this->setFlashMessage('Déconnexion réussie.');

        return $this->redirect($response, '/');
    }

    private function logoutUser(): void
    {
        unset($_SESSION['user']);
    }

    private function setFlashMessage(string $message): void
    {
        $_SESSION['flash'] = $message;
    }

    private function redirect(Response $response, string $url): Response
    {
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
