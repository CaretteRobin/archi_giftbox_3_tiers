<?php

namespace Tests;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\WebUI\Actions\Api\ListPrestationsApiAction;
use Mockery;

class ListPrestationsApiActionTest extends TestCase
{
    private $action;
    private $catalogueService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->catalogueService = Mockery::mock(CatalogueServiceInterface::class);
        $this->action = new ListPrestationsApiAction($this->catalogueService);
    }

    public function testInvokeReturnsAllPrestations()
    {
        // Arrange
        $request = $this->createMockRequest();
        [$response, $stream] = $this->createMockResponse();

        $categories = [
            ['id' => 1, 'libelle' => 'Restaurant'],
            ['id' => 2, 'libelle' => 'Hébergement']
        ];

        $prestations1 = [
            ['id' => 1, 'titre' => 'Dîner gastronomique', 'cat_id' => 1]
        ];

        $prestations2 = [
            ['id' => 2, 'titre' => 'Nuit en hôtel 5 étoiles', 'cat_id' => 2]
        ];

        // Configurer le comportement du mock CatalogueService
        $this->catalogueService->shouldReceive('getCategories')
            ->once()
            ->andReturn($categories);

        $this->catalogueService->shouldReceive('getPrestationsByCategory')
            ->with(1)
            ->once()
            ->andReturn($prestations1);

        $this->catalogueService->shouldReceive('getPrestationsByCategory')
            ->with(2)
            ->once()
            ->andReturn($prestations2);

        // Configurer le comportement attendu pour l'écriture de la réponse
        $expectedPrestations = array_merge($prestations1, $prestations2);
        $expectedResponse = [
            "type" => "collection",
            "count" => count($expectedPrestations),
            "prestations" => $expectedPrestations
        ];

        $stream->shouldReceive('write')
            ->with(json_encode($expectedResponse))
            ->once();

        // Act
        $result = ($this->action)($request, $response);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testInvokeWithEmptyCategories()
    {
        // Arrange
        $request = $this->createMockRequest();
        [$response, $stream] = $this->createMockResponse();

        // Configurer le comportement du mock CatalogueService
        $this->catalogueService->shouldReceive('getCategories')
            ->once()
            ->andReturn([]);

        // Configurer le comportement attendu pour l'écriture de la réponse
        $expectedResponse = [
            "type" => "collection",
            "count" => 0,
            "prestations" => []
        ];

        $stream->shouldReceive('write')
            ->with(json_encode($expectedResponse))
            ->once();

        // Act
        $result = ($this->action)($request, $response);

        // Assert
        $this->assertSame($response, $result);
    }
}