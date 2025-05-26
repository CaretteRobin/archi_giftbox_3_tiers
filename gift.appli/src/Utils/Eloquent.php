<?php
declare(strict_types=1);

namespace Gift\Appli\Utils;

use Dotenv\Dotenv;                       // <-- AJOUT
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Singleton Eloquent.
 * Charge automatiquement le fichier .env au premier appel.
 */
final class Eloquent
{
    private static ?Capsule $instance = null;

    public static function getInstance(): Capsule
    {
        if (self::$instance === null) {
            self::boot();
        }
        return self::$instance;
    }

    private static function boot(): void
    {
        // -------------------------------------------------------------
        // 1. Chargement des variables d’environnement (.env)
        // -------------------------------------------------------------
        $rootCandidates = [
            dirname(__DIR__),
            dirname(__DIR__, 2),
            dirname(__DIR__, 3),
            dirname(__DIR__, 4),
        ];

        foreach ($rootCandidates as $root) {
            if (file_exists($root . '/.env')) {
                Dotenv::createImmutable($root)->safeLoad();
                break;
            }
        }

        // -------------------------------------------------------------
        // 2. Configuration de l’ORM
        // -------------------------------------------------------------
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

    private function __construct() {}
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new \LogicException('Cannot unserialize singleton');
    }
}