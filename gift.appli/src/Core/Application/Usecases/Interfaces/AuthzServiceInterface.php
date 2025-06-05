<?php

namespace Gift\Appli\Core\Application\Usecases\Interfaces;


use Gift\Appli\Core\Domain\Entities\Box;
use Gift\Appli\Core\Domain\Entities\User;

interface AuthzServiceInterface
{
    // Définition des opérations sous forme de constantes
    public const CREATE_BOX = 'create_box';
    public const VIEW_BOX = 'view_box';
    public const VALIDATE_BOX = 'validate_box';
    public const ADD_PRESTATION = 'add_prestation';
    public const GENERATE_URL = 'generate_url';

    /**
     * Vérifie si l'utilisateur est autorisé à effectuer une opération
     *
     * @param User $user L'utilisateur à vérifier
     * @param string $operation L'opération à autoriser
     * @param Box|null $box La box concernée (si applicable)
     * @return bool True si l'utilisateur est autorisé, false sinon
     */
    public function isAuthorized(User $user, string $operation, ?Box $box = null): bool;
}