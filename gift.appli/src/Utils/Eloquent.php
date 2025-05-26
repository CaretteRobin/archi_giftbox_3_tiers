<?php
declare(strict_types=1);

namespace Gift\Appli\Utils;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Gestionnaire Eloquent sous forme de singleton.
 *
 * – Charge automatiquement les variables d’environnement depuis le fichier .env placé à
 *   la racine du projet (une seule fois pour tout le cycle de vie du processus).
 * – Expose l’instance Capsule via Eloquent::getInstance().
 */
final class Eloquent
{
    /** @var Capsule|null */
    private static ?Capsule $instance = null;

    /**
     * Retourne l’instance unique de Capsule. La connexion est
     * initialisée au premier appel.
     */
    public static function getInstance(): Capsule
    {
        if (self::$instance === null) {
            self::boot();
        }

        return self::$instance;
    }

    /**
     * Initialise Eloquent à partir des variables d’environnement.
     * Appel interne déclenché une seule fois.
     */
    private static function boot(): void
    {
        // -----------------------------------------------------------------
        // 1. Chargement des variables d’environnement (.env)
        // -----------------------------------------------------------------
        // On remonte de trois niveaux : Utils/ → src/ → <racine du dépôt>
        $projectRoot = dirname(__DIR__, 3);
        if (file_exists($projectRoot . '/.env')) {
            Dotenv::createImmutable($projectRoot)->safeLoad();
        }

        // -----------------------------------------------------------------
        // 2. Création et configuration de l’instance Capsule
        // -----------------------------------------------------------------
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => $_ENV['DB_DRIVER']    ?? 'mysql',
            'host'      => $_ENV['DB_HOST']      ?? '127.0.0.1',
            'database'  => $_ENV['DB_DATABASE']  ?? '',
            'username'  => $_ENV['DB_USERNAME']  ?? '',
            'password'  => $_ENV['DB_PASSWORD']  ?? '',
            'charset'   => $_ENV['DB_CHARSET']   ?? 'utf8mb4',
            'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
            'prefix'    => $_ENV['DB_PREFIX']    ?? '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$instance = $capsule;
    }

    /**
     * Empêche l’instanciation externe.
     */
    private function __construct() {}

    /**
     * Empêche le clonage.
     */
    private function __clone() {}

    /**
     * Empêche la restauration depuis une chaîne.
     */
    public function __wakeup(): void
    {
        throw new \LogicException('Cannot unserialize singleton');
    }
}