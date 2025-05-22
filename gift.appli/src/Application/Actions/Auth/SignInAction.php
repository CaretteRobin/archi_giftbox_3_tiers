<?php
declare(strict_types=1);

namespace Gift\Appli\Application\Actions\Auth;

use Gift\Appli\Core\Domain\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SignInAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = $request->getParsedBody();
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            $this->setFlashMessage('Email ou mot de passe manquant.');
            return $this->redirect($response, '/auth');
        }

        $user = User::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            $this->setFlashMessage('Identifiants incorrects.');
            return $this->redirect($response, '/auth');
        }

        $_SESSION['user'] = $user->id;
        $this->setFlashMessage('Connexion réussie.');
        return $this->redirect($response, '/');
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
