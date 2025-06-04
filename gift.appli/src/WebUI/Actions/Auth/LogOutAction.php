<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogOutAction
{

    use FlashRedirectTrait;

    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->authProvider->logout();
        return $this->redirectWithFlash(
            $response,
            '/',
            'Déconnexion réussie.',
            'success'
        );
    }
}
