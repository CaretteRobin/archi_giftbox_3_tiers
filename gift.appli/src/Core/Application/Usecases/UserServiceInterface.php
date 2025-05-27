<?php
namespace Gift\Appli\Core\Application\Usecases;

interface UserServiceInterface
{
    public function getUserById(string $userId): array;

    public function getUserBoxes(string $userId): array;

    public function getUserBoxStats(string $userId): array;

    public function isAdmin(string $userId): bool;
}
