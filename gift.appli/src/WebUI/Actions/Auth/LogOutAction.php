<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Application\Usecases\Interfaces\UserServiceInterface;
use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Gift\Appli\WebUI\Providers\UserProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogOutAction
{

    use FlashRedirectTrait;

    private AuthServiceInterface $authService;
    private UserProviderInterface $userProvider;

    public function __construct(AuthServiceInterface $authService, UserProviderInterface $userProvider)
    {
        $this->authService = $authService;
        $this->userProvider = $userProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->userProvider->clear();
        return $this->redirectWithFlash(
            $response,
            '/',
            'Déconnexion réussie.',
            'success'
        );
    }
}