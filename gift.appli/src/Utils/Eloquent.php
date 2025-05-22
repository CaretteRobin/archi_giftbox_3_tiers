<?php
declare(strict_types=1);

namespace Gift\Appli\Utils;

use Illuminate\Database\Capsule\Manager as Capsule;

class Eloquent
{
    public static function init(string $configFile): void
    {
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException("Fichier de configuration introuvable : $configFile");
        }

        $config = parse_ini_file($configFile);
        if ($config === false) {
            throw new \RuntimeException("Erreur lors de la lecture du fichier de configuration.");
        }

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'    => $config['driver'] ?? 'mysql',
            'host'      => $config['host'] ?? '127.0.0.1',
            'database'  => $config['database'] ?? '',
            'username'  => $config['username'] ?? '',
            'password'  => $config['password'] ?? '',
            'charset'   => $config['charset'] ?? 'utf8mb4',
            'collation' => $config['collation'] ?? 'utf8mb4_unicode_ci',
            'prefix'    => $config['prefix'] ?? '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
