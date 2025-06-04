<?php

namespace Tests;

use Gift\Appli\WebUI\Actions\Api\ApiAction;
use Mockery;

class ApiActionTest extends TestCase
{
    private $apiAction;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une instance concrète de la classe abstraite pour les tests
        $this->apiAction = new class extends ApiAction {
            public function test($response, $data, $status = 200)
            {
                return $this->jsonResponse($response, $data, $status);
            }
        };
    }

    public function testJsonResponseFormatsDataCorrectly()
    {
        // Arrange
        [$response, $stream] = $this->createMockResponse();
        $data = ['message' => 'Test réussi'];

        // Configurer les attentes spécifiques
        $stream->shouldReceive('write')
            ->with(json_encode($data))
            ->once();

        // Act
        $result = $this->apiAction->test($response, $data);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testJsonResponseWithCustomStatus()
    {
        // Arrange
        [$response, $stream] = $this->createMockResponse();
        $data = ['error' => 'Resource not found'];

        // Configurer les attentes spécifiques
        $response->shouldReceive('withStatus')
            ->with(404)
            ->once()
            ->andReturnSelf();

        // Act
        $result = $this->apiAction->test($response, $data, 404);

        // Assert
        $this->assertSame($response, $result);
    }
}