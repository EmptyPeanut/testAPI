<?php
// require_once './app/PremiereClass.php';
declare(strict_types=1);
// require './app/Dispatcher/UsersDispatcher.php';

require 'vendor/autoload.php';

use App\Controllers\UsersController;
use App\Dispatchers\UsersDispatcher;

date_default_timezone_set('Europe/Paris');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$URI = $_SERVER['REQUEST_URI'];
$explodedURI = explode('/', $URI);

switch ($explodedURI[1]) {
    case 'user':
        $users = new UsersDispatcher($URI);
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