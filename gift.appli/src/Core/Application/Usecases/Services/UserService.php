<?php

namespace Gift\Appli\Core\Application\Usecases\Services;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Gift\Appli\Core\Domain\Entities\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class UserService implements UserServiceInterface
{
    public function getUserById(string $id): array
    {
        try {
            return User::findOrFail($id)->toArray();
        } catch (ModelNotFoundException $e) {
            throw new EntityNotFoundException("User $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error retrieving user: " . $e->getMessage());
        }
    }

    public function getUserBoxes(string $userId): array
    {
        try {
            return User::findOrFail($userId)->boxes()->with('prestations')->get()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error retrieving user's boxes: " . $e->getMessage());
        }
    }

    public function getUserBoxStats(string $userId): array
    {
        try {
            return User::findOrFail($userId)->statistiquesBoxes();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error retrieving user's box stats: " . $e->getMessage());
        }
    }

    public function getUserTotalSpent(string $userId): float
    {
        try {
            return User::findOrFail($userId)->totalValeurBoxes();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error calculating total spent: " . $e->getMessage());
        }
    }

    /**
     * @throws EntityNotFoundException
     * @throws InternalErrorException
     */
    public function isAdmin(string $userId): bool
    {
        try {
            $user = User::findOrFail($userId);
            return $user->role === User::ROLE_ADMIN;
        } catch (ModelNotFoundException $e) {
            throw new EntityNotFoundException("User $userId not found");
        } catch (Throwable $e) {
            throw new InternalErrorException("Error checking admin status: " . $e->getMessage());
        }

    }

    public function storeUserInSession(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = $user;
    }

    public function getUserFromSession(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    public function removeUserFromSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user']);
    }

    public function updateUser(string $userId, array $data): bool
    {
        try {
            $user = User::findOrFail($userId);
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            return $user->update($data);
        } catch (ModelNotFoundException $e) {
            throw new EntityNotFoundException("Utilisateur $userId introuvable");
        } catch (Throwable $e) {
            throw new InternalErrorException("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    public function deleteUser(string $userId): bool
    {
        try {
            $user = User::findOrFail($userId);
            return $user->delete();
        } catch (ModelNotFoundException $e) {
            throw new EntityNotFoundException("Utilisateur $userId introuvable");
        } catch (Throwable $e) {
            throw new InternalErrorException("Erreur lors de la suppression : " . $e->getMessage());
        }
    }
}
