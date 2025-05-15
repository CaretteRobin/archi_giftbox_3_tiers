<?php
namespace Gift\Infrastructure\Services;

use Gift\Core\Application\Usecases\CatalogueServiceInterface;
use Gift\Core\Application\Usecases\Exceptions\EntityNotFoundException;
use Gift\Core\Application\Usecases\Exceptions\InternalErrorException;
use Gift\Core\Domain\Entities\Categorie;

class CatalogueService implements CatalogueServiceInterface {

    public function getCategories(): array {
        try {
            $rawCategories = \gift\modeles\Categorie::all(); // Eloquent
            $result = [];
            foreach ($rawCategories as $cat) {
                $result[] = new Categorie(
                    id: (int)$cat->id,
                    libelle: $cat->libelle,
                    description: $cat->description
                );
            }
            return $result;
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur en récupérant les catégories.");
        }
    }

    public function getCategorieById(int $id): array {
        try {
            $cat = \gift\modeles\Categorie::findOrFail($id);
            return $cat->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Catégorie $id non trouvée");
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur interne.");
        }
    }

    public function getPrestationById(string $id): array {
        try {
            $p = \gift\modeles\Prestation::findOrFail($id);
            return $p->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Prestation $id non trouvée");
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur interne.");
        }
    }

    public function getPrestationsbyCategorie(int $categ_id): array {
        try {
            $p = \gift\modeles\Prestation::where('categorie_id', $categ_id)->get();
            return $p->toArray();
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur interne.");
        }
    }

    public function getThemesCoffrets(): array {
        // TODO : à implémenter plus tard
        return [];
    }

    public function getCoffretById(int $id): array {
        // TODO : à implémenter plus tard
        return [];
    }
}
