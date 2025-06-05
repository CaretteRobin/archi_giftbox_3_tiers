<?php

namespace Tests;

use Gift\Appli\WebUI\Actions\Api\ApiAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ApiActionTest extends TestCase
{
    /**
     * Classe concrète qui étend ApiAction pour tester les méthodes protégées
     */
    private $apiAction;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer une classe anonyme qui étend ApiAction pour pouvoir tester les méthodes protégées
        $this->apiAction = new class extends ApiAction {
            public function jsonResponsePublic($response, $data, int $status = 200): ResponseInterface
            {
                return $this->jsonResponse($response, $data, $status);
            }
        };
    }

    /**
     * Crée une réponse HTTP simulée avec un stream pour les tests
     *
     * @return array [ResponseInterface, StreamInterface]
     */
    protected function createMockResponse()
    {
        // Créer un mock pour le stream
        $stream = $this->createMock(StreamInterface::class);

        // Créer un mock pour la réponse
        $response = $this->createMock(ResponseInterface::class);

        // Configurer le stream pour capturer le contenu écrit
        $streamContent = '';
        $stream->method('write')
            ->willReturnCallback(function ($content) use (&$streamContent) {
                $streamContent .= $content;
                return strlen($content);
            });

        $stream->method('__toString')
            ->willReturnCallback(function () use (&$streamContent) {
                return $streamContent;
            });

        // Configurer la réponse pour retourner le stream et se retourner elle-même
        $response->method('getBody')
            ->willReturn($stream);

        $response->method('withHeader')
            ->willReturnSelf();

        $response->method('withStatus')
            ->willReturnSelf();

        return [$response, $stream];
    }

    /**
     * Test que jsonResponse formate correctement les données en JSON
     */
    public function testJsonResponseFormatsDataCorrectly(): void
    {
        // Arrangement
        [$response, $stream] = $this->createMockResponse();
        $testData = ['name' => 'Test', 'value' => 123];

        // Action
        $result = $this->apiAction->jsonResponsePublic($response, $testData);

        // Assertion
        $this->assertEquals(json_encode($testData), $stream->__toString());
        $this->assertSame($response, $result);
    }

    /**
     * Test que jsonResponse définit correctement un statut personnalisé
     */
    public function testJsonResponseWithCustomStatus(): void
    {
        // Arrangement
        [$response, $stream] = $this->createMockResponse();
        $testData = ['error' => 'Not found'];
        $customStatus = 404;

        // On s'attend à ce que withStatus soit appelé avec le statut personnalisé
        $response->expects($this->once())
            ->method('withStatus')
            ->with($this->equalTo($customStatus))
            ->willReturnSelf();

        // Action
        $result = $this->apiAction->jsonResponsePublic($response, $testData, $customStatus);

        // Assertion
        $this->assertEquals(json_encode($testData), $stream->__toString());
        $this->assertSame($response, $result);
    }

    /**
     * Test que jsonResponse définit le bon Content-Type
     */
    public function testJsonResponseSetsContentTypeHeader(): void
    {
        // Arrangement
        [$response, $stream] = $this->createMockResponse();
        $testData = ['test' => true];

        // On s'attend à ce que withHeader soit appelé avec le bon Content-Type
        $response->expects($this->once())
            ->method('withHeader')
            ->with(
                $this->equalTo('Content-Type'),
                $this->equalTo('application/json')
            )
            ->willReturnSelf();

        // Action
        $result = $this->apiAction->jsonResponsePublic($response, $testData);

        // Assertion
        $this->assertSame($response, $result);
    }
}