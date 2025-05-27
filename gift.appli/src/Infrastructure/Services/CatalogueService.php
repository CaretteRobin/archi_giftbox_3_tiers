<?php

namespace Gift\Appli\Infrastructure\Services;

use Gift\Appli\Core\Application\Usecases\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Core\Domain\Entities\CoffretType;
use Gift\Appli\Core\Domain\Entities\Theme;

class CatalogueService implements CatalogueServiceInterface
{
    public function getCategories(): array
    {
        try {
            return Categorie::all()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Failed to fetch categories: " . $e->getMessage());
        }
    }

    public function getCategoryById(int $id): array
    {
        try {
            return Categorie::findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            throw new EntityNotFoundException("Category $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getPrestationById(string $id): array
    {
        try {
            return Prestation::findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            throw new EntityNotFoundException("Prestation $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getPrestationsByCategory(int $categoryId): array
    {
        try {
            return Prestation::where('cat_id', $categoryId)->get()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getThemesWithCoffrets(): array
    {
        try {
            return Theme::with('coffretTypes')->get()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Failed to load themes with coffrets: " . $e->getMessage());
        }
    }

    public function getCoffretById(int $id): array
    {
        try {
            return CoffretType::with('prestations')->findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            throw new EntityNotFoundException("Coffret $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }
}
