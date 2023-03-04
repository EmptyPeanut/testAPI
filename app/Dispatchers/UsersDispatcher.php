<?

namespace App\Dispatchers;

use App\Controllers\UsersController;

class UsersDispatcher{

    private $explodedURI;
    private $usersController;
    
    public function __construct(string $URI)
    {
        $this->explodedURI = explode('/', substr($URI, 6));
        $this->usersController = new UsersController($URI);
        $this->dispatch($this->explodedURI);
    }
    
    public function dispatch(array $explodedURI){
        return match ($explodedURI[0]) {
            'findAll'   => $this->usersController->getAllUsers(),
            'add'       => $this->usersController->addUser(),
            'findOne'   => $this->usersController->findOne(),
            'update'    => $this->usersController->modifyUser(),
            'connect'   => $this->usersController->connection(),
            default     => $this->usersController->getAllUsers()
        };
    }

    
}