<?php
declare(strict_types=1);

namespace App\Dispatchers;

use App\Controllers\UsersController;

class UsersDispatcher{

    // private $explodedURI;
    private $usersController;
    
    public function __construct(array $explodedURI)
    {
        $this->usersController = new UsersController($explodedURI);
        $this->dispatch($explodedURI);
    }

    public function dispatch(array $explodedURI){
        return match ($explodedURI[2]) {
            'findAll'   => $this->usersController->getAllUsers(),
            'add'       => $this->usersController->addUser(),
            'findOne'   => $this->usersController->findOne(),
            'update'    => $this->usersController->modifyUser(),
            'connect'   => $this->usersController->connection(),
            default     => $this->usersController->getAllUsers()
        };
    }

    
}