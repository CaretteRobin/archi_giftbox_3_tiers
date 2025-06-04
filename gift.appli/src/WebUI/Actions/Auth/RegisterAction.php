<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterAction
{
    use FlashRedirectTrait;

    private AuthServiceInterface $authService;
    private UserProviderInterface $userProvider;

    public function __construct(
        AuthServiceInterface $authService,
        UserProviderInterface $userProvider
    ) {
        $this->authService = $authService;
        $this->userProvider = $userProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                return $this->redirectWithFlash(
                    $response,
                    '/auth',
                    'Email ou mot de passe manquant.',
                    'error'
                );
            }

            $user = $this->authService->register($email, $password);
            $this->userProvider->store($user);

            return $this->redirectWithFlash(
                $response,
                '/',
                'Inscription réussie ! Bienvenue.',
                'success'
            );

        } catch (InternalErrorException $e) {
            return $this->redirectWithFlash(
                $response,
                '/auth',
                $e->getMessage(),
                'error'
            );
        }
    }
}