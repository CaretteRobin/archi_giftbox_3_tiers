<?php

namespace Gift\Appli\Core\Application\Usecases;

interface AuthorizationServiceInterface
{
    // Constantes pour les opérations
    const int CREATE_BOX = 1;
    const int VIEW_BOX = 2;
    const int VALIDATE_BOX = 3;
    const int ADD_PRESTATION_TO_BOX = 4;
    const int GENERATE_URL = 5;

    /**
     * Vérifie si l'utilisateur est autorisé à effectuer une opération
     *
     * @param int $operation L'opération à vérifier
     * @param string|null $boxId L'ID de la box concernée (si applicable)
     * @return bool True si l'utilisateur est autorisé, false sinon
     */
    public function isAuthorized(int $operation, ?string $boxId = null): bool;

    /**
     * Vérifie l'autorisation et lance une exception si non autorisé
     *
     * @param int $operation L'opération à vérifier
     * @param string|null $boxId L'ID de la box concernée (si applicable)
     * @throws \Gift\Appli\Core\Application\Exceptions\BoxException Si l'utilisateur n'est pas autorisé
     */
    public function checkAuthorization(int $operation, ?string $boxId = null): void;
}