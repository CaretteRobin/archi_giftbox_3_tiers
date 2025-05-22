<?php
declare(strict_types=1);

namespace Gift\Appli\Application\Actions\Auth;

use Gift\Appli\Core\Domain\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;

class RegisterAction
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

        if (User::where('email', $email)->exists()) {
            $this->setFlashMessage('Cet email est déjà utilisé.');
            return $this->redirect($response, '/auth');
        }

        try {
            $user = new User();
            $user->id = Uuid::uuid4()->toString();
            $user->email = $email;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->role = User::ROLE_CLIENT;
            $user->save();

            $_SESSION['user'] = $user->id;
            $this->setFlashMessage('Inscription réussie ! Bienvenue.');
            return $this->redirect($response, '/');
        } catch (\Exception $e) {
            $this->setFlashMessage('Une erreur est survenue lors de l’inscription.');
            return $this->redirect($response, '/auth');
        }
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
