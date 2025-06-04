<?php

namespace Gift\Appli\Core\Application\Usecases\Services;

use Gift\Appli\Core\Application\Usecases\Interfaces\AuthzServiceInterface;
use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\Core\Domain\Entities\User;

class AuthzService implements AuthzServiceInterface
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