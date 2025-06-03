<?php
namespace Gift\Appli\WebUI\Providers;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Domain\Entities\User;
use Ramsey\Uuid\Uuid;

class UserProvider
{
    /**
     * Enregistre un nouvel utilisateur
     */
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

    /**
     * Authentifie un utilisateur
     */
    public function authenticate(string $email, string $password): ?User
    {
        try {
            $user = User::where('email', $email)->first();
            if (!$user || !password_verify($password, $user->password)) {
                return null;
            }

            return $user;
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur d'authentification : " . $e->getMessage());
        }
    }

    /**
     * Vérifie si un email est déjà utilisé
     */
    public function isEmailTaken(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public function getUserById(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Récupère un utilisateur par son email
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->save();
    }
}