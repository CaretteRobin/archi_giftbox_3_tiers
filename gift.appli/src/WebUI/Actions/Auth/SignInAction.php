<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Actions\Auth;

use Gift\Appli\Core\Application\Exceptions\EntityNotFoundException;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthServiceInterface;
use Gift\Appli\Core\Domain\Traits\FlashRedirectTrait;
use Gift\Appli\WebUI\Providers\UserProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SignInAction
{
    use FlashRedirectTrait;

    private AuthServiceInterface $authService;
    private UserProvider $userProvider;

    public function __construct(
        AuthServiceInterface $authService,
        UserProvider $userProvider
    ) {
        $this->authService  = $authService;
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

            // On récupère directement l'utilisateur complet
            $user = $this->authService->login($email, $password);
            $this->userProvider->store($user);

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