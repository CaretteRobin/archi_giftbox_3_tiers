<?php
namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Crée une réponse HTTP simulée avec un stream pour les tests
     *
     * @return array [ResponseInterface, StreamInterface]
     */
    protected function createMockResponse()
    {
        // Créer un mock pour le stream
        $stream = $this->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Créer un mock pour la réponse
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

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
}