<?php

declare(strict_types=1);

namespace Gift\Appli\WebUI\Providers;

use Exception;

/**
 * Classe de gestion des tokens CSRF
 */
class CsrfTokenProvider
{
    /**
     * Nom de la clé de session pour stocker le token CSRF
     */
    private const SESSION_KEY = 'csrf_token';

    /**
     * Génère un token CSRF, le stocke en session et le retourne
     *
     * @return string Le token généré
     */
    public static function generate(): string
    {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Génération d'un token aléatoire
        $token = bin2hex(random_bytes(32));

        // Stockage en session
        $_SESSION[self::SESSION_KEY] = $token;

        return $token;
    }

    /**
     * Vérifie si le token fourni correspond à celui stocké en session
     * Supprime le token en session après vérification (usage unique)
     *
     * @param string|null $token Le token à vérifier
     * @return bool True si le token est valide
     * @throws Exception Si le token est invalide ou manquant
     */
    public static function check(?string $token): bool
    {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier l'existence du token en session
        if (!isset($_SESSION[self::SESSION_KEY])) {
            throw new Exception('Le token CSRF n\'existe pas en session');
        }

        // Récupérer le token stocké
        $storedToken = $_SESSION[self::SESSION_KEY];

        // Supprimer le token de la session (usage unique)
        unset($_SESSION[self::SESSION_KEY]);

        // Vérifier que le token reçu n'est pas vide
        if ($token === null || $token === '') {
            throw new Exception('Aucun token CSRF n\'a été fourni');
        }

        // Comparer les tokens avec une fonction de comparaison sécurisée
        // pour éviter les attaques par timing
        if (!hash_equals($storedToken, $token)) {
            throw new Exception('Le token CSRF est invalide');
        }

        return true;
    }
}