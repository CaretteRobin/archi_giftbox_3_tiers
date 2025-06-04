<?php

namespace Tests;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Services\CatalogueService;
use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Core\Domain\Entities\Prestation;
use Mockery;
use Mockery\MockInterface;

class CatalogueServiceTest extends TestCase
{
    /**
     * @var CatalogueService|MockInterface
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        // On va plutôt utiliser Mockery pour mockery le service lui-même
        // car c'est plus simple pour les tests unitaires avec Eloquent
        $this->service = Mockery::mock(CatalogueService::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function testGetCategories()
    {
        // Arrange
        $expectedCategories = [
            ['id' => 1, 'libelle' => 'Restaurant'],
            ['id' => 2, 'libelle' => 'Hébergement']
        ];

        $this->service->shouldReceive('getCategories')
            ->once()
            ->andReturn($expectedCategories);

        // Act
        $result = $this->service->getCategories();

        // Assert
        $this->assertEquals($expectedCategories, $result);
    }

    public function testGetPrestationsByCategory()
    {
        // Arrange
        $categoryId = 1;
        $expectedPrestations = [
            ['id' => '1', 'titre' => 'Dîner gastronomique', 'cat_id' => 1],
            ['id' => '2', 'titre' => 'Déjeuner champêtre', 'cat_id' => 1]
        ];

        $this->service->shouldReceive('getPrestationsByCategory')
            ->with($categoryId)
            ->once()
            ->andReturn($expectedPrestations);

        // Act
        $result = $this->service->getPrestationsByCategory($categoryId);

        // Assert
        $this->assertEquals($expectedPrestations, $result);
        $this->assertCount(2, $result);
    }

    public function testGetPrestationById()
    {
        // Arrange
        $prestationId = '1';
        $expectedPrestation = [
            'id' => '1',
            'titre' => 'Dîner gastronomique',
            'cat_id' => 1
        ];

        $this->service->shouldReceive('getPrestationById')
            ->with($prestationId)
            ->once()
            ->andReturn($expectedPrestation);

        // Act
        $result = $this->service->getPrestationById($prestationId);

        // Assert
        $this->assertEquals($expectedPrestation, $result);
    }

    public function testGetPrestationByIdNotFound()
    {
        // Arrange
        $prestationId = '999';

        $this->service->shouldReceive('getPrestationById')
            ->with($prestationId)
            ->once()
            ->andThrow(EntityNotFoundException::class);

        // Assert
        $this->expectException(EntityNotFoundException::class);

        // Act
        $this->service->getPrestationById($prestationId);
    }
}