<?php
namespace Gift\Appli\Core\Application\Usecases\Services;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CoffretServiceInterface;
use Gift\Appli\Core\Domain\Entities\CoffretType;
use Gift\Appli\Core\Domain\Entities\Theme;

class CoffretService implements CoffretServiceInterface
{
    public function getAllCoffrets(): array
    {
        try {
            return CoffretType::with('prestations')->get()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Failed to retrieve coffrets: " . $e->getMessage());
        }
    }

    public function getCoffretById(int $id): array
    {
        try {
            return CoffretType::with('prestations')->findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Coffret ID $id not found.");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getCoffretsByTheme(int $themeId): array
    {
        try {
            return CoffretType::where('theme_id', $themeId)->with('prestations')->get()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error fetching coffrets by theme: " . $e->getMessage());
        }
    }

    public function listThemes(): array
    {
        try {
            return Theme::all()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error retrieving themes: " . $e->getMessage());
        }
    }

    public function getThemeById(int $id): array
    {
        try {
            return Theme::findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Theme ID $id not found.");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getPrestationsForCoffret(int $coffretId): array
    {
        try {
            $coffret = CoffretType::findOrFail($coffretId);
            return $coffret->prestations()->get()->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Coffret ID $coffretId not found.");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Error retrieving prestations: " . $e->getMessage());
        }
    }

    public function getCoffretTypes(): array
    {
        // TODO: Implement getCoffretTypes() method.
    }

    public function getCoffretTypeById(int $id): array
    {
        // TODO: Implement getCoffretTypeById() method.
    }

    public function getBoxByToken(string $token): array
    {
        // TODO: Implement getBoxByToken() method.
    }

    public function createBox(array $data): string
    {
        // TODO: Implement createBox() method.
    }

    public function addPrestationToBox(string $boxId, string $prestationId, int $quantity = 1): void
    {
        // TODO: Implement addPrestationToBox() method.
    }

    public function validateBox(string $boxId): bool
    {
        // TODO: Implement validateBox() method.
    }

    public function updateBoxAmount(string $boxId): float
    {
        // TODO: Implement updateBoxAmount() method.
    }
}
