<?php

namespace Tests;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Services\CatalogueService;
use Gift\Appli\Core\Domain\Entities\Categorie;
use Gift\Appli\Core\Domain\Entities\Prestation;
use Gift\Appli\Core\Domain\Entities\CoffretType;
use Gift\Appli\Core\Domain\Entities\Theme;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;

class CatalogueServiceTest extends TestCase
{
    /**
     * @var CatalogueService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Utiliser un vrai service pour tester la logique
        $this->service = new CatalogueService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetCategories()
    {
        // Arrangement
        $expectedCategories = [
            ['id' => 1, 'libelle' => 'Restaurant'],
            ['id' => 2, 'libelle' => 'Hébergement']
        ];

        // Mock de la classe Categorie
        $categorieMock = Mockery::mock('alias:' . Categorie::class);
        $categorieMock->shouldReceive('all->toArray')
            ->once()
            ->andReturn($expectedCategories);

        // Action
        $result = $this->service->getCategories();

        // Assertion
        $this->assertEquals($expectedCategories, $result);
    }

    public function testGetCategoryById()
    {
        // Arrangement
        $categoryId = 1;
        $expectedCategory = ['id' => 1, 'libelle' => 'Restaurant'];

        // Mock de la classe Categorie
        $categorieMock = Mockery::mock('alias:' . Categorie::class);
        $categorieMock->shouldReceive('findOrFail')
            ->with($categoryId)
            ->once()
            ->andReturn(Mockery::mock([
                'toArray' => $expectedCategory
            ]));

        // Action
        $result = $this->service->getCategoryById($categoryId);

        // Assertion
        $this->assertEquals($expectedCategory, $result);
    }

    public function testGetCategoryByIdNotFound()
    {
        // Arrangement
        $categoryId = 999;

        // Mock de la classe Categorie
        $categorieMock = Mockery::mock('alias:' . Categorie::class);
        $categorieMock->shouldReceive('findOrFail')
            ->with($categoryId)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Assertion
        $this->expectException(EntityNotFoundException::class);

        // Action
        $this->service->getCategoryById($categoryId);
    }

    public function testGetPrestationById()
    {
        // Arrangement
        $prestationId = '1';
        $expectedPrestation = [
            'id' => '1',
            'libelle' => 'Dîner gastronomique',
            'cat_id' => 1
        ];

        // Mock de la classe Prestation
        $prestationMock = Mockery::mock('alias:' . Prestation::class);
        $prestationMock->shouldReceive('findOrFail')
            ->with($prestationId)
            ->once()
            ->andReturn(Mockery::mock([
                'toArray' => $expectedPrestation
            ]));

        // Action
        $result = $this->service->getPrestationById($prestationId);

        // Assertion
        $this->assertEquals($expectedPrestation, $result);
    }

    public function testGetPrestationByIdNotFound()
    {
        // Arrangement
        $prestationId = '999';

        // Mock de la classe Prestation
        $prestationMock = Mockery::mock('alias:' . Prestation::class);
        $prestationMock->shouldReceive('findOrFail')
            ->with($prestationId)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Assertion
        $this->expectException(EntityNotFoundException::class);

        // Action
        $this->service->getPrestationById($prestationId);
    }

    public function testGetPrestationsByCategory()
    {
        // Arrangement
        $categoryId = 1;
        $expectedPrestations = [
            ['id' => '1', 'libelle' => 'Dîner gastronomique', 'cat_id' => 1],
            ['id' => '2', 'libelle' => 'Déjeuner champêtre', 'cat_id' => 1]
        ];

        // Mock de la collection
        $collectionMock = Mockery::mock();
        $collectionMock->shouldReceive('toArray')
            ->once()
            ->andReturn($expectedPrestations);

        // Mock de la requête
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('get')
            ->once()
            ->andReturn($collectionMock);

        // Mock de la classe Prestation
        $prestationMock = Mockery::mock('alias:' . Prestation::class);
        $prestationMock->shouldReceive('where')
            ->with('cat_id', $categoryId)
            ->once()
            ->andReturn($queryMock);

        // Action
        $result = $this->service->getPrestationsByCategory($categoryId);

        // Assertion
        $this->assertEquals($expectedPrestations, $result);
        $this->assertCount(2, $result);
    }

    public function testGetThemesWithCoffrets()
    {
        // Arrangement
        $expectedThemes = [
            [
                'id' => 1,
                'libelle' => 'Aventure',
                'coffret_types' => [
                    ['id' => 1, 'libelle' => 'Aventure Extrême']
                ]
            ]
        ];

        // Mock de la classe Theme
        $themeMock = Mockery::mock('alias:' . Theme::class);
        $themeMock->shouldReceive('with->get->toArray')
            ->once()
            ->andReturn($expectedThemes);

        // Action
        $result = $this->service->getThemesWithCoffrets();

        // Assertion
        $this->assertEquals($expectedThemes, $result);
    }

    public function testGetCoffretById()
    {
        // Arrangement
        $coffretId = 1;
        $expectedCoffret = [
            'id' => 1,
            'libelle' => 'Aventure Extrême',
            'prestations' => [
                ['id' => '1', 'libelle' => 'Saut en parachute']
            ]
        ];

        // Mock de la classe CoffretType
        $coffretMock = Mockery::mock('alias:' . CoffretType::class);
        $coffretMock->shouldReceive('with->findOrFail')
            ->with($coffretId)
            ->once()
            ->andReturn(Mockery::mock([
                'toArray' => $expectedCoffret
            ]));

        // Action
        $result = $this->service->getCoffretById($coffretId);

        // Assertion
        $this->assertEquals($expectedCoffret, $result);
    }

    public function testGetCoffretByIdNotFound()
    {
        // Arrangement
        $coffretId = 999;

        // Mock de la classe CoffretType
        $coffretMock = Mockery::mock('alias:' . CoffretType::class);
        $coffretMock->shouldReceive('with->findOrFail')
            ->with($coffretId)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Assertion
        $this->expectException(EntityNotFoundException::class);

        // Action
        $this->service->getCoffretById($coffretId);
    }

    public function testInternalErrorHandling()
    {
        // Arrangement
        $categoriesMock = Mockery::mock('alias:' . Categorie::class);
        $categoriesMock->shouldReceive('all')
            ->once()
            ->andThrow(new \Exception("Database connection error"));

        // Assertion
        $this->expectException(InternalErrorException::class);

        // Action
        $this->service->getCategories();
    }
}