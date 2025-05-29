<?php

namespace Core\Application\Services\Authorization;

use Core\Domain\Entities\User;
use Core\Domain\Entities\Box;

class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * Vérifie si l'utilisateur est autorisé à effectuer une opération
     *
     * @param User $user L'utilisateur à vérifier
     * @param string $operation L'opération à autoriser
     * @param Box|null $box La box concernée (si applicable)
     * @return bool True si l'utilisateur est autorisé, false sinon
     */
    public function isAuthorized(User $user, string $operation, ?Box $box = null): bool
    {
        // Vérifier que l'utilisateur a un rôle >= 1 pour toutes les opérations
        if ($user->getRole() < 1) {
            return false;
        }

        // Pour les opérations nécessitant d'être propriétaire
        $ownerOperations = [
            self::VIEW_BOX,
            self::VALIDATE_BOX,
            self::ADD_PRESTATION,
            self::GENERATE_URL
        ];

        if (in_array($operation, $ownerOperations)) {
            // Vérifier que la box est fournie
            if ($box === null) {
                return false;
            }

            // Vérifier que l'utilisateur est propriétaire de la box
            return $box->getUserId() === $user->getId();
        }

        // Pour CREATE_BOX, seul le rôle >= 1 est nécessaire, ce qui est déjà vérifié
        return true;
    }
}