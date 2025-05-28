<?php

namespace Gift\Appli\Infrastructure\Services;

use Gift\Appli\Core\Application\Usecases\AuthServiceInterface;
use Gift\Appli\Core\Domain\Entities\User;
use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Ramsey\Uuid\Uuid;

class AuthService implements AuthServiceInterface
{
    public function register(string $email, string $password): string
    {
        try {
            if ($this->isEmailTaken($email)) {
                throw new InternalErrorException("Email déjà utilisé.");
            }

            $user = new User();
            $user->id = Uuid::uuid4()->toString();
            $user->email = $email;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->role = User::ROLE_CLIENT;
            $user->save();

            return $user->id;
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur d'inscription : " . $e->getMessage());
        }
    }

    public function login(string $email, string $password): string
    {
        try {
            $user = User::where('email', $email)->first();
            if (!$user || !password_verify($password, $user->password)) {
                throw new EntityNotFoundException("Email ou mot de passe incorrect.");
            }

            return $user->id;
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user']);
    }

    public function isEmailTaken(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
