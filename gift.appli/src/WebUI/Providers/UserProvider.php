<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Providers;

use Gift\Appli\Core\Domain\Entities\User;
use Gift\Appli\WebUI\Providers\Interfaces\UserProviderInterface;

/**
 * Gestion centralisée de l’utilisateur connecté.
 * Aucune autre classe ne doit manipuler directement la session.
 */
class UserProvider implements UserProviderInterface
{
    /** Clef utilisée dans $_SESSION */
    private const SESSION_KEY = 'user';

    /**
     * Enregistre l’utilisateur (sans le mot de passe) dans la session.
     */
    public function store(User $user): void
    {
        $this->startSessionIfNeeded();

        // Toutes les colonnes sauf le mot de passe
        $userData = $user->toArray();
        unset($userData['password']);

        $_SESSION[self::SESSION_KEY] = $userData;
    }

    /**
     * Renvoie l’utilisateur actuellement connecté ou null.
     */
    public function current(): ?User
    {
        $this->startSessionIfNeeded();

        $data = $_SESSION[self::SESSION_KEY] ?? null;
        if ($data === null) {
            return null;
        }

        // On re-matérialise un objet User (le mot de passe reste absent)
        $user = new User();
        $user->forceFill($data);   // méthode Eloquent pour remplir sans protection
        return $user;
    }

    /**
     * Supprime l’utilisateur stocké (déconnexion).
     */
    public function clear(): void
    {
        $this->startSessionIfNeeded();
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Indique si un utilisateur est connecté.
     */
    public function isLoggedIn(): bool
    {
        $this->startSessionIfNeeded();
        return isset($_SESSION[self::SESSION_KEY]);
    }

    /* -------------------------------------------------------------------- */
    /* Helpers                                                              */
    /* -------------------------------------------------------------------- */

    private function startSessionIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}