<?php

namespace Gift\Appli\Infrastructure\Services;

use Gift\Appli\Core\Application\Usecases\UserServiceInterface;
use Gift\Appli\Core\Domain\Entities\User;
use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;

class UserService implements UserServiceInterface
{
    public function getUserById(string $id): array
    {
        try {
            return User::findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
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
}
