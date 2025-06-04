<?php

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Crée un mock de réponse PSR-7
     */
    protected function createMockResponse()
    {
        $stream = Mockery::mock('Psr\Http\Message\StreamInterface');
        $stream->shouldReceive('write')->withAnyArgs()->andReturnSelf();

        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn($stream);
        $response->shouldReceive('withHeader')->withAnyArgs()->andReturnSelf();
        $response->shouldReceive('withStatus')->withAnyArgs()->andReturnSelf();

        return [$response, $stream];
    }

    /**
     * Crée un mock de requête PSR-7
     */
    protected function createMockRequest(array $queryParams = [])
    {
        $request = Mockery::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getQueryParams')
            ->andReturn($queryParams);

        $request->shouldReceive('withAttribute')
            ->andReturnSelf();

        return $request;
    }
}