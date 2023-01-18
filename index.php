<?php
// require_once './app/PremiereClass.php';
require 'vendor/autoload.php';
use App\Controllers\UsersController;


$URI = $_SERVER['REQUEST_URI'];

if (!empty($URI)) {
    switch ($URI) {
        case str_starts_with($URI, '/user'):
            $secondClasse = new UsersController();
            break;
        
        default:
            header('HTTP/1.1 404 Please enter a valid URL', true, 404);
            break;
    }
}else {
    header('HTTP/1.1 404 Not Found', true, 404);
}


?>