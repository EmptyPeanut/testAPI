<?php
// require_once './app/PremiereClass.php';
declare(strict_types=1);
// phpinfo();
require '../vendor/autoload.php';
use App\Helper;
use App\Dispatchers\UsersDispatcher;


date_default_timezone_set('Europe/Paris');
set_error_handler(array("App\Helper", "errorLog"));
set_exception_handler(array("App\Helper", "exceptionLog"));

// trigger_error("Ceci est une erreur non fatale", E_USER_WARNING);
// function divide($a, $b) {
//     if ($b === 0) {
//         throw new Exception("Division by zero");
//     }

//     return $a / $b;
// }


//     echo divide(10, 0);
// die(var_dump(__DIR__));
$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

$URI = $_SERVER['REQUEST_URI'];
$explodedURI = explode('/', $URI);

switch ($explodedURI[1]) {
    case 'user':
        new UsersDispatcher($explodedURI);
        break;
    
    default:
        # code...
        break;
}

// if (!empty($URI)) {
//     match ($explodedURI[1]) {
//         'user'  => new UsersDispatcher($URI),
//         'user'  => var_dump($explodedURI),
//         default => header('HTTP/1.1 404 Not Found', true, 404)
//     };
// }


?>