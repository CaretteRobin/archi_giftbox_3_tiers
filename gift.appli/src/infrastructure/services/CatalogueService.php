<?php
namespace gift\appli\infrastructure\services;

use gift\appli\core\domain\entities\Categorie;
use Gift\Core\Application\Usecases\CatalogueServiceInterface;
use Gift\Core\Application\Usecases\Exceptions\EntityNotFoundException;
use Gift\Core\Application\Usecases\Exceptions\InternalErrorException;


class CatalogueService implements CatalogueServiceInterface {

    public function getCategories(): array {
        try {
            $rawCategories = Categorie::all(); // Eloquent
            $result = [];
            foreach ($rawCategories as $cat) {
                $result[] = new Categorie(
                    (int) $cat->id,
                    $cat->libelle,
                    $cat->description
                );
            }
            return $result;
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur en récupérant les catégories.");
        }
    }

    public function getCategorieById(int $id): array {
        try {
            $cat = Categorie::findOrFail($id);
            return $cat->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Catégorie $id non trouvée");
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur interne.");
        }
    }

    public function getPrestationById(string $id): array {
        try {
            $p = Prestation::findOrFail($id);
            return $p->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Prestation $id non trouvée");
        } catch (\Exception $e) {
            throw new InternalErrorException("Erreur interne.");
        }
    }

    public function getPrestationsbyCategorie(int $categ_id): array {
        try {
            $p = Prestation::where('categorie_id', $categ_id)->get();
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
