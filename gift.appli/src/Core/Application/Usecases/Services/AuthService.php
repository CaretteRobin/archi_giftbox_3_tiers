<?php

namespace Gift\Appli\Core\Application\Usecases\Services;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Domain\Entities\User;
use Ramsey\Uuid\Uuid;

class AuthService implements AuthServiceInterface
{
    public function register(string $email, string $password): User
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

            return $user;
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur d'inscription : " . $e->getMessage());
        }
    }

    public function login(string $email, string $password): User
    {
        try {
            $user = User::where('email', $email)->first();
            if (!$user || !password_verify($password, $user->password)) {
                throw new EntityNotFoundException("Email ou mot de passe incorrect.");
            }

            return $user;
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur de connexion : " . $e->getMessage());
        }
    }

    private function isEmailTaken(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
