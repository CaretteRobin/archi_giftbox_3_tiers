<?php

namespace Gift\Appli\Core\Application\Usecases\Interfaces;

interface CatalogueServiceInterface {
    public function getCategories();

    public function getCategoryById(int $id);

    public function getPrestationById(string $id);

    public function getPrestationsByCategory(int $categoryId);

    public function getThemesWithCoffrets();

    public function getCoffretById(int $id);
}
