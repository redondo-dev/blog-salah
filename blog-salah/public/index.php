<?php
// Afficher les erreurs en développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Définir le chemin racine
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);

// Charger l'autoloader de Composer
require ROOT_PATH . 'vendor/autoload.php';

// Autoloader personnalisé
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = APP_PATH . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Router simple
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Sécuriser les entrées
$controller = htmlspecialchars($controller, ENT_QUOTES, 'UTF-8');
$action = htmlspecialchars($action, ENT_QUOTES, 'UTF-8');

// Construire le nom du contrôleur
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = APP_PATH . 'controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        // Action non trouvée
        header("HTTP/1.0 404 Not Found");
        echo "Action non trouvée";
    }
} else {
    // Contrôleur non trouvé
    header("HTTP/1.0 404 Not Found");
    echo "Page non trouvée";
}
