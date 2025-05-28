<?php

namespace Gift\Appli\Core\Application\Usecases\Interfaces;

interface CatalogueServiceInterface {
    public function getCategories(): array;

    public function getCategoryById(int $id): array;

    public function getPrestationById(string $id): array;

    public function getPrestationsByCategory(int $categoryId): array;

    public function getThemesWithCoffrets(): array;

    public function getCoffretById(int $id): array;
}
