<?php

namespace Gift\Appli\Infrastructure\Services;

use Gift\Appli\Core\Application\Exceptions\BoxException;
use Gift\Appli\Core\Application\Usecases\AuthorizationServiceInterface;
use Gift\Appli\Core\Application\Usecases\AuthServiceInterface;
use Illuminate\Database\Capsule\Manager as DB;

class AuthorizationService implements AuthorizationServiceInterface
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorized(int $operation, ?string $boxId = null): bool
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$this->authService->isAuthenticated()) {
            return false;
        }

        // Récupérer l'utilisateur actuel
        $currentUser = $this->authService->getCurrentUser();

        // Vérifier le rôle minimum (toutes les opérations nécessitent un rôle >= 1)
        if (!isset($currentUser['role']) || $currentUser['role'] < 1) {
            return false;
        }

        // Pour créer une box, seul le rôle est vérifié
        if ($operation === self::CREATE_BOX) {
            return true;
        }

        // Pour les autres opérations, vérifier que l'utilisateur est propriétaire de la box
        if ($boxId !== null && in_array($operation, [
                self::VIEW_BOX,
                self::VALIDATE_BOX,
                self::ADD_PRESTATION_TO_BOX,
                self::GENERATE_URL
            ])) {
            return $this->isBoxOwner($currentUser['id'], $boxId);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function checkAuthorization(int $operation, ?string $boxId = null): void
    {
        if (!$this->isAuthorized($operation, $boxId)) {
            throw new BoxException("Opération non autorisée", BoxException::UNAUTHORIZED_USER);
        }
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de la box
     *
     * @param string $userId ID de l'utilisateur
     * @param string $boxId ID de la box
     * @return bool True si l'utilisateur est propriétaire, false sinon
     */
    private function isBoxOwner(string $userId, string $boxId): bool
    {
        $box = DB::table('box')
            ->where('id', $boxId)
            ->where('createur_id', $userId)
            ->first();

        return $box !== null;
    }
}