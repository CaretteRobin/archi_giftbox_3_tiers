<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Exceptions\InternalErrorException;
use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterAction
{
    use FlashRedirectTrait;

    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
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

            // Le AuthProvider gère à la fois l'inscription et le stockage en session
            $this->authProvider->register($email, $password);

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