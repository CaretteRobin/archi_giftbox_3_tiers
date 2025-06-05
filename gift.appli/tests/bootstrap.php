<?php
require_once __DIR__ . '/../src/vendor/autoload.php';
// Autoloader personnalisé pour les classes de test
spl_autoload_register(function ($class) {
    // Pour les classes dans le namespace Tests
    if (strpos($class, 'Tests\\') === 0) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 6)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});
