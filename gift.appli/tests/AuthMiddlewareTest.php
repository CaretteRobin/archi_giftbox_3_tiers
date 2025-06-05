<?php

namespace Tests;

use Gift\Appli\Core\Domain\Entities\User;
use Gift\Appli\WebUI\Middlewares\AuthMiddleware;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Mockery;

class AuthMiddlewareTest extends TestCase
{
    private $middleware;
    private $authProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authProvider = Mockery::mock(AuthProviderInterface::class);

        // Créer le middleware avec une implémentation personnalisée de redirectWithFlash
        $this->middleware = new class($this->authProvider) extends AuthMiddleware {
            // Signature exacte comme dans le trait
            protected function redirectWithFlash(
                ResponseInterface $response,
                string $url,
                string $message,
                string $type = 'info'
            ): ResponseInterface {
                // Retourner simplement une réponse mock pour les tests
                $mockResponse = Mockery::mock(ResponseInterface::class);
                $mockResponse->shouldReceive('getStatusCode')->andReturn(302);
                return $mockResponse;
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Crée une requête HTTP simulée pour les tests
     *
     * @return ServerRequestInterface
     */
    protected function createMockRequest()
    {
        return Mockery::mock(ServerRequestInterface::class)
            ->shouldReceive('withAttribute')
            ->andReturnSelf()
            ->getMock();
    }

    public function testProcessWithAuthenticatedUser()
    {
        // Arrange
        $request = $this->createMockRequest();
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $user = Mockery::mock(User::class);

        $this->authProvider->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn(true);

        $this->authProvider->shouldReceive('getLoggedUser')
            ->once()
            ->andReturn($user);

        $handler->shouldReceive('handle')
            ->once()
            ->andReturn($response);

        // Act
        $result = $this->middleware->process($request, $handler);

        // Assert
        $this->assertSame($response, $result);
    }

    public function testProcessWithUnauthenticatedUser()
    {
        // Arrange
        $request = $this->createMockRequest();
        $handler = Mockery::mock(RequestHandlerInterface::class);

        $this->authProvider->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn(false);

        // Handler ne devrait pas être appelé
        $handler->shouldReceive('handle')
            ->never();

        // Act
        $result = $this->middleware->process($request, $handler);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(302, $result->getStatusCode());
    }

    public function testProcessWithLoggedInButUserNotFound()
    {
        // Arrange
        $request = $this->createMockRequest();
        $handler = Mockery::mock(RequestHandlerInterface::class);

        $this->authProvider->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn(true);

        $this->authProvider->shouldReceive('getLoggedUser')
            ->once()
            ->andReturn(null);

        // Handler ne devrait pas être appelé
        $handler->shouldReceive('handle')
            ->never();

        // Act
        $result = $this->middleware->process($request, $handler);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(302, $result->getStatusCode());
    }
}