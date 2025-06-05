<?php

namespace Tests;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\WebUI\Actions\Api\ListPrestationsApiAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Crée une requête HTTP simulée pour les tests
     */
    protected function createMockRequest()
    {
        return Mockery::mock(ServerRequestInterface::class);
    }

    public function testInvokeReturnsAllPrestations()
    {
        // Arrangement
        $request = $this->createMockRequest();
        [$response, $stream] = $this->createMockResponse();

        $categories = [
            ['id' => 1, 'libelle' => 'Restaurant'],
            ['id' => 2, 'libelle' => 'Hébergement']
        ];

        $prestations1 = [
            ['id' => 1, 'libelle' => 'Dîner gastronomique', 'cat_id' => 1]
        ];

        $prestations2 = [
            ['id' => 2, 'libelle' => 'Nuit en hôtel 5 étoiles', 'cat_id' => 2]
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

        // Action
        $result = ($this->action)($request, $response);

        // Vérification
        $expectedPrestations = array_merge($prestations1, $prestations2);
        $expectedResponse = [
            "type" => "collection",
            "count" => count($expectedPrestations),
            "prestations" => $expectedPrestations
        ];

        $this->assertEquals(json_encode($expectedResponse), $stream->__toString());
        $this->assertSame($response, $result);
    }

    public function testInvokeWithEmptyCategories()
    {
        // Arrangement
        $request = $this->createMockRequest();
        [$response, $stream] = $this->createMockResponse();

        // Configurer le comportement du mock CatalogueService
        $this->catalogueService->shouldReceive('getCategories')
            ->once()
            ->andReturn([]);

        // Action
        $result = ($this->action)($request, $response);

        // Vérification
        $expectedResponse = [
            "type" => "collection",
            "count" => 0,
            "prestations" => []
        ];

        $this->assertEquals(json_encode($expectedResponse), $stream->__toString());
        $this->assertSame($response, $result);
    }
}