<?php

namespace Gift\Appli\Core\Application\Usecases\Interfaces;

interface UserServiceInterface
{

    // Dans UserServiceInterface.php
    public static function storeUser(array $user): void;

    public static function getUser(): ?array;

    public static function removeUser(): void;

    public function getUserById(string $userId): array;

    public function getUserBoxes(string $userId): array;

    public function getUserBoxStats(string $userId): array;

    public function getUserTotalSpent(string $userId): float;

    public function isAdmin(string $userId): bool;

    public function updateUser(string $userId, array $data): bool;

    public function deleteUser(string $userId): bool;

}