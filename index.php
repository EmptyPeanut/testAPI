<?php
// require_once './app/PremiereClass.php';
declare(strict_types=1);

require 'vendor/autoload.php';

use App\Controllers\UsersController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$URI = $_SERVER['REQUEST_URI'];

if (!empty($URI)) {
    switch ($URI) {
        case str_starts_with($URI, '/user/'):
            $users = new UsersController($URI);
            $users->dispatcher();
            break;
        
        default:
            header('HTTP/1.1 404 Please enter a valid URL', true, 404);
            break;
    }
}else {
    header('HTTP/1.1 404 Not Found', true, 404);
}


?>