<?php

namespace Gift\Appli\Core\Application\Usecases;

interface AuthServiceInterface
{
    /**
     * Authentifie un utilisateur avec son email et mot de passe
     *
     * @param string $email L'email de l'utilisateur
     * @param string $password Le mot de passe en clair
     * @return array|null Les données de l'utilisateur ou null si l'authentification échoue
     */
    public function authenticate(string $email, string $password): ?array;

    /**
     * Vérifie si un utilisateur est actuellement authentifié
     *
     * @return bool True si l'utilisateur est authentifié, false sinon
     */
    public function isAuthenticated(): bool;

    /**
     * Déconnecte l'utilisateur actuellement authentifié
     *
     * @return bool True si la déconnexion a réussi
     */
    public function logout(): bool;

    /**
     * Récupère les informations de l'utilisateur actuellement authentifié
     *
     * @return array|null Les données de l'utilisateur ou null s'il n'est pas authentifié
     */
    public function getCurrentUser(): ?array;

    /**
     * Enregistre un nouvel utilisateur dans le système
     *
     * @param string $email L'email du nouvel utilisateur
     * @param string $password Le mot de passe en clair
     * @param int $role Le rôle de l'utilisateur (par défaut: utilisateur standard)
     * @return string|null L'identifiant de l'utilisateur créé ou null en cas d'échec
     */
    public function register(string $email, string $password, int $role = 1): ?string;

    /**
     * Vérifie si l'utilisateur courant a un rôle spécifique
     *
     * @param int $role Le rôle à vérifier
     * @return bool True si l'utilisateur a le rôle spécifié
     */
    public function hasRole(int $role): bool;
}