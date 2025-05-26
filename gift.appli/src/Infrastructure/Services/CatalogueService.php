<?php
namespace Gift\Appli\Infrastructure\Services;

use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Core\Application\Usecases\CatalogueServiceInterface;
use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;

class CatalogueService implements CatalogueServiceInterface
{
    public function getCategories(): array
    {
        try {
            // On renvoie toutes les catégories sous forme de tableau « brut »
            return Categorie::all()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException(
                "Erreur lors de la récupération des catégories : " . $e->getMessage()
            );
        }
    }

    public function getCategorieById(int $id): array
    {
        try {
            return Categorie::findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Catégorie $id non trouvée");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur interne : " . $e->getMessage());
        }
    }

    public function getPrestationById(string $id): array
    {
        try {
            return Prestation::findOrFail($id)->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Prestation $id non trouvée");
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur interne : " . $e->getMessage());
        }
    }

    public function getPrestationsbyCategorie(int $categ_id): array
    {
        try {
            return Prestation::where('categorie_id', $categ_id)->get()->toArray();
        } catch (\Throwable $e) {
            throw new InternalErrorException("Erreur interne : " . $e->getMessage());
        }
    }

    /* … */
    public function getThemesCoffrets(): array
    {
        // TODO: Implement getThemesCoffrets() method.
        return [];
    }

    public function getCoffretById(int $id): array
    {
        // TODO: Implement getCoffretById() method.
        return [];
    }
}