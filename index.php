<?php
// require_once './app/PremiereClass.php';
require 'vendor/autoload.php';
use App\Users;


$URI = $_SERVER['REQUEST_URI'];

if (!empty($URI)) {
    switch ($URI) {
        case '/user/findall':
            $secondClasse = new Users();
            break;
        
        default:
            header('HTTP/1.1 404 Please enter a valid URL', true, 404);
            break;
    }
}else {
    header('HTTP/1.1 404 Not Found', true, 404);
}


?>