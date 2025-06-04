<?php

namespace Tests;

use Gift\Appli\Core\Domain\Entities\User;
use Gift\Appli\WebUI\Middlewares\AuthMiddleware;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mockery;

class AuthMiddlewareTest extends TestCase
{
    private $middleware;
    private $userProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userProvider = Mockery::mock(UserProviderInterface::class);

        // Créer le middleware avec une implémentation personnalisée de redirectWithFlash
        $this->middleware = new class($this->userProvider) extends AuthMiddleware {
            public function redirectWithFlash($request, $path, $message, $type)
            {
                // Retourner simplement une réponse mock pour les tests
                $response = Mockery::mock(ResponseInterface::class);
                $response->shouldReceive('getStatusCode')->andReturn(302);
                return $response;
            }
        };
    }

    public function testProcessWithAuthenticatedUser()
    {
        // Arrange
        $request = $this->createMockRequest();
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $user = Mockery::mock(User::class);

        $this->userProvider->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn(true);

        $this->userProvider->shouldReceive('current')
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

        $this->userProvider->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn(false);

        // Act
        $result = $this->middleware->process($request, $handler);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(302, $result->getStatusCode());
    }
}