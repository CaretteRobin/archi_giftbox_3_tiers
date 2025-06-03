<?php

namespace Gift\Appli\WebUI\Middlewares;


use gift\app\WebUI\Exceptions\UnauthorizedException;
use Gift\Appli\Core\Application\Usecases\Interfaces\AuthorizationServiceInterface;
use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\Core\Domain\Entities\User;

class AuthorizationMiddleware
{
    private AuthorizationServiceInterface $authorizationService;

    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Vérifie l'autorisation pour une opération donnée
     *
     * @param User $user L'utilisateur authentifié
     * @param string $operation L'opération à vérifier
     * @param Box|null $box La box concernée (si applicable)
     * @throws UnauthorizedException Si l'utilisateur n'est pas autorisé
     */
    public function checkAuthorization(User $user, string $operation, ?Box $box = null): void
    {
        if (!$this->authorizationService->isAuthorized($user, $operation, $box)) {
            throw new UnauthorizedException("Vous n'êtes pas autorisé à effectuer cette opération");
        }
    }
}
