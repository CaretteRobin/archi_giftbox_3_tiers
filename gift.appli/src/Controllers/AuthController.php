<?php

declare(strict_types=1);

namespace gift\appli\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use gift\appli\Models\User;
use Ramsey\Uuid\Uuid; // ✅ Import nécessaire pour les UUIDs

class AuthController
{
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Inscription
    public function register(Request $request, Response $response): Response
    {
        $this->startSession();
        $data = $request->getParsedBody();

        if (empty($data['email']) || empty($data['password'])) {
            $_SESSION['flash'] = 'Email ou mot de passe manquant.';
            return $response->withHeader('Location', '/auth')->withStatus(302);
        }

        if (User::where('email', $data['email'])->exists()) {
            $_SESSION['flash'] = 'Cet email est déjà utilisé.';
            return $response->withHeader('Location', '/auth')->withStatus(302);
        }

        $user = new User();
        $user->id = Uuid::uuid4()->toString(); // ✅ Génération de l'UUID
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->role = User::ROLE_CLIENT;
        $user->save();

        $_SESSION['user'] = $user->id;
        $_SESSION['flash'] = 'Inscription réussie ! Bienvenue.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    // Connexion
    public function signin(Request $request, Response $response): Response
    {
        $this->startSession();
        $data = $request->getParsedBody();

        if (empty($data['email']) || empty($data['password'])) {
            $_SESSION['flash'] = 'Email ou mot de passe manquant.';
            return $response->withHeader('Location', '/auth')->withStatus(302);
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user || !password_verify($data['password'], $user->password)) {
            $_SESSION['flash'] = 'Identifiants incorrects.';
            return $response->withHeader('Location', '/auth')->withStatus(302);
        }

        $_SESSION['user'] = $user->id;
        $_SESSION['flash'] = 'Connexion réussie.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    // Déconnexion
    public function logout(Request $request, Response $response): Response
    {
        $this->startSession();

        unset($_SESSION['user']);
        $_SESSION['flash'] = 'Déconnexion réussie.';

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function form(Request $request, Response $response): Response
    {
        return Twig::fromRequest($request)->render($response, 'pages/auth.twig');
    }

}
