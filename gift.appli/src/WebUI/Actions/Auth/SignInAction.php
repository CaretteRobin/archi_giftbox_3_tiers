<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SignInAction
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

            // On récupère directement l'utilisateur complet
            $this->authProvider->login($email, $password);

            return $this->redirectWithFlash(
                $response,
                '/',
                'Connexion réussie.',
                'success'
            );

        } catch (EntityNotFoundException) {
            return $this->redirectWithFlash(
                $response,
                '/auth',
                'Identifiants incorrects.',
                'error'
            );
        }
    }
}