<?php

namespace Gift\Appli\Core\Application\Usecases\Services;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Core\Domain\Entities\CoffretType;
use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Core\Domain\Entities\Theme;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CatalogueService implements CatalogueServiceInterface
{
    public function getCategories(): Collection
    {
        try {
            return Categorie::all();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Failed to fetch categories: " . $e->getMessage());
        }
    }

    public function getCategoryById(int $id): Categorie
    {
        try {
            return Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            throw new EntityNotFoundException("Category $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getPrestationById(string $id): Prestation
    {
        try {
            return Prestation::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            throw new EntityNotFoundException("Prestation $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getPrestationsByCategory(int $categoryId): Collection
    {
        try {
            return Prestation::where('cat_id', $categoryId)->get();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }

    public function getThemesWithCoffrets(): Collection
    {
        try {
            return Theme::with('coffretTypes')->get();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Failed to load themes with coffrets: " . $e->getMessage());
        }
    }

    public function getCoffretById(int $id): Collection
    {
        try {
            return CoffretType::with('prestations')->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new EntityNotFoundException("Coffret $id not found");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Internal error: " . $e->getMessage());
        }
    }
}
