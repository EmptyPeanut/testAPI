<?php
namespace App\Controllers;

use Utils\PDOUtils;
use App\Models\Users;

class UsersController {
    
    private $model;

    function __construct(){
        $this->model = new Users();
    }

    public function dispatcher(string $URI){
        $p = (substr($URI, 6));
        switch ($p) {
        case 'findall':
            $this->getAllUsers();
            break;
        case 'add':
            $this->addUser();
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
}
?>