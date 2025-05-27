<?php

namespace Gift\Appli\Infrastructure\Services;

use Gift\Appli\Core\Application\Usecases\AuthServiceInterface;
use Illuminate\Database\Capsule\Manager as DB;
use Ramsey\Uuid\Uuid;

class AuthService implements AuthServiceInterface
{
    /**
     * Authentifie un utilisateur avec son email et mot de passe
     *
     * @param string $email L'email de l'utilisateur
     * @param string $password Le mot de passe en clair
     * @return array|null Les données de l'utilisateur ou null si l'authentification échoue
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = DB::table('user')
            ->where('email', $email)
            ->first();

        if (!$user) {
            return null;
        }

        // Vérification du mot de passe
        if (!password_verify($password, $user->password)) {
            return null;
        }

        // Stockage des informations de l'utilisateur en session
        $_SESSION['user'] = [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ];

        return $_SESSION['user'];
    }

    /**
     * Vérifie si un utilisateur est actuellement authentifié
     *
     * @return bool True si l'utilisateur est authentifié, false sinon
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Déconnecte l'utilisateur actuellement authentifié
     *
     * @return bool True si la déconnexion a réussi
     */
    public function logout(): bool
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            return true;
        }

        return false;
    }

    /**
     * Récupère les informations de l'utilisateur actuellement authentifié
     *
     * @return array|null Les données de l'utilisateur ou null s'il n'est pas authentifié
     */
    public function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Enregistre un nouvel utilisateur dans le système
     *
     * @param string $email L'email du nouvel utilisateur
     * @param string $password Le mot de passe en clair
     * @param int $role Le rôle de l'utilisateur (par défaut: utilisateur standard)
     * @return string|null L'identifiant de l'utilisateur créé ou null en cas d'échec
     */
    public function register(string $email, string $password, int $role = 1): ?string
    {
        // Vérifier si l'email existe déjà
        $existingUser = DB::table('user')
            ->where('email', $email)
            ->first();

        if ($existingUser) {
            return null; // L'email est déjà utilisé
        }

        // Générer un ID unique
        $id = Uuid::uuid4()->toString();

        // Hasher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insérer le nouvel utilisateur
        $result = DB::table('user')->insert([
            'id' => $id,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);

        return $result ? $id : null;
    }

    /**
     * Vérifie si l'utilisateur courant a un rôle spécifique
     *
     * @param int $role Le rôle à vérifier
     * @return bool True si l'utilisateur a le rôle spécifié
     */
    public function hasRole(int $role): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return $_SESSION['user']['role'] === $role;
    }
}