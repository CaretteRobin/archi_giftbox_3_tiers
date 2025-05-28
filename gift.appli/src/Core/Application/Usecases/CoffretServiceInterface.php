<?php
namespace Gift\Appli\Core\Application\Usecases;

interface CoffretServiceInterface
{
    public function getCoffretTypes(): array;

    public function getCoffretTypeById(int $id): array;

    public function getBoxByToken(string $token): array;

    public function createBox(array $data): string;

    public function addPrestationToBox(string $boxId, string $prestationId, int $quantity = 1): void;

    public function validateBox(string $boxId): bool;

    public function updateBoxAmount(string $boxId): float;
}
