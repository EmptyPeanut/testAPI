<?php
namespace App\Controllers;

use Utils\PDOUtils;
use App\Models\Users;

class UsersController {
    
    private $model;
    private $URI;
    private $explodedURI;

    function __construct(string $URI){
        $this->model = new Users();
        $this->URI = $URI;
        $this->explodedURI = explode('/', substr($this->URI, 6));
    }

    public function dispatcher(){
        
        switch ($this->explodedURI[0]) {

        case 'findall':
            $this->getAllUsers();
            break;

        case 'add':
            $this->addUser();
            break;

        case 'findOne':
            $this->findOne();
            break;

        case 'update':
            $this->modifyUser();
            break;
        
        default:
            $this->getAllUsers();
            break;
        }
    }
    
    
    
    
    
    /**
     * Get data from all every users in the database
     * @return JSON of every users
     */
    public function getAllUsers(){
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $result_set = $this->model->getAllUsers();
            $result = [];
            foreach ($result_set as $set) {
                array_push(
                    $result,
                    [
                        "id" => $set["id"],
                        "username" => $set["username"]
                    ]
                );
            }
            if (!is_null($result)) {
                echo (json_encode($result));
            }
        }else {
            header('HTTP/1.1 405', true, 405);
        }
    }
    
    /**
     * Create a user from the data passed in the body request
     */
    public function addUser(){
        $data = json_decode(file_get_contents('php://input'), true);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!is_null($data) && !empty($data)) {

                if (isset($data['username']) && !empty($data['username']) && isset($data['password']) && !empty($data['password'])) {
                    if (gettype($data['username']) == "string" && gettype($data['password']) == "string") {
                        $this->model->addUser($data['username'], $data['password']);
                    } else {
                        header('HTTP/1.1 400 Wrong type', true, 400);
                    }
                } else {
                    header('HTTP/1.1 400 Username and password are not set', true, 400);
                }
            } else {
                header('HTTP/1.1 400 Sometihng went wrong trying to get the request body', true, 400);
            }
        }else {
            header('HTTP/1.1 405', true, 405);
        }  
    }

    public function modifyUser(){
        $data = json_decode(file_get_contents('php://input'), true);

        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            if (count($this->explodedURI)== 2 && isset($this->explodedURI[1]) && !is_null($this->explodedURI[1]) && !empty($this->explodedURI[1])) {
                $userId = (int)$this->explodedURI[1];
                if (!empty($userId)) {
                    var_dump('Hello');
                }else {
                    header('HTTP/1.1 405 Id of wrong type', true, 405);
                }
            }else {
                header('HTTP/1.1 405 No id found', true, 405);
            }
            
            
            // if (!is_null($data) && !empty($data)) {

            //     if (isset($data['username']) && !empty($data['username']) && isset($data['password']) && !empty($data['password'])) {
            //         if (gettype($data['username']) == "string" && gettype($data['password']) == "string") {
            //             $this->model->addUser($data['username'], $data['password']);
            //         } else {
            //             header('HTTP/1.1 400 Wrong type', true, 400);
            //         }
            //     } else {
            //         header('HTTP/1.1 400 Username and password are not set', true, 400);
            //     }
            // } else {
            //     header('HTTP/1.1 400 Sometihng went wrong trying to get the request body', true, 400);
            // }
        } else {
            header('HTTP/1.1 405', true, 405);
        } 
    }

    public function findOne(){
        $result = [];

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (count($this->explodedURI) == 2 && isset($this->explodedURI[1]) && !is_null($this->explodedURI[1]) && !empty($this->explodedURI[1])) {
                $userId = (int)$this->explodedURI[1];
                if (!empty($userId)) {
                    $result_set = $this->model->getUser($userId)[0];
                    if (!is_null($result_set) && !empty($result_set)) {
                        $result = [
                            "id" => $result_set["id"],
                            "username" => $result_set["username"]
                        ];
                    }else {
                        $result = [
                            "error" => "Aucun utilisateur trouvé"
                        ];
                    }
                    
                } else {
                    header('HTTP/1.1 405 Id of wrong type', true, 405);
                }
            } else {
                header('HTTP/1.1 405 No id found', true, 405);
            }
        } else {
            header('HTTP/1.1 405', true, 405);
        }
        echo(json_encode($result));
    }
}
?>