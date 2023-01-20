<?php
namespace App\Controllers;


use App\Helper;
use App\Models\Users;


class UsersController {
    
    private $model;
    private $helper;
    private $URI;
    private $explodedURI;

    function __construct(string $URI){
        $this->model = new Users();
        $this->helper = new Helper();
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
        case 'connect':
            $this->connection();
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

            $result_set     = $this->model->getAllUsers();
            $result         = [];
            $result['data'] = [];

            foreach ($result_set as $set) {
                array_push(
                    $result['data'],
                    [
                        "id"            => $set["id"],
                        "username"      => $set["username"],
                        "firstName"     => $set["first_name"],
                        "lastName"       => $set["last_name"],
                        "age"           => $set["age"]
                    ]
                );
            }

            if (!is_null($result)) {
                $result['code'] = 200;
                $this->helper->returnJson($result);
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

                if (isset($data['username']) && !empty($data['username'])
                    && isset($data['firstName']) && !empty($data['firstName'])
                    && isset($data['lastName']) && !empty($data['lastName'])
                    && isset($data['password']) && !empty($data['password'])
                    ) {

                    if (   gettype($data['username'])   == "string"
                        && gettype($data['password'])   == "string"
                        && gettype($data['firstName'])  == "string"
                        && gettype($data['lastName'])   == "string"
                        ) {
                        
                        $hashedPwd = password_hash($data["password"], PASSWORD_DEFAULT);
                        try {

                            $this->helper->returnJson([
                                "code" => "200"
                            ]);
                            $this->model->addUser($data['firstName'], $data['lastName'], $data['username'], $hashedPwd, $data['age']);

                        } catch (\Throwable $th) {

                            echo(json_encode([
                                "message" => "Something wrong happenned while adding your account to the database"
                            ]));

                        }
                        
                    } else {
                        
                        $this->helper->returnJson([
                            "code"      => 400,
                            "message"   => "Input of wrong type"
                        ]);

                        header('HTTP/1.1 400 Wrong type', true, 400);

                    }
                } else {

                    $this->helper->returnJson([
                        "code"      => 400,
                        "message"   => "There are missing fields"
                    ]);

                    header('HTTP/1.1 400 Username and password are not set', true, 400);

                }
            } else {

                $this->helper->returnJson([   
                    "code"      => 400,
                    "message"   => "Something went wrong trying to get the request body"
                ]);

                header('HTTP/1.1 400', true, 400);
            }
        }else {
            header('HTTP/1.1 405', true, 405);
        }  
    }

    //TODO: Fonction incmplète ici et dans le model, ne sait pas encore comment la modification se fera
    public function modifyUser(){
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            $data = json_decode(file_get_contents('php://input'), true);
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
        } else {
            header('HTTP/1.1 405', true, 405);
        } 
    }

    /**
     * Get data of a specific user by his Id 
     */
    public function findOne(){
        $result = [];

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            if (count($this->explodedURI) == 2 
                && isset($this->explodedURI[1])
                && !is_null($this->explodedURI[1])
                && !empty($this->explodedURI[1])
                ) {

                $userId = (int)$this->explodedURI[1];

                if (!empty($userId)) {
                    $result_set = $this->model->getUser($userId)[0];
                    if (!is_null($result_set) && !empty($result_set)) {
                        $result = [
                            "data" => array(
                                "id"            => $result_set["id"],
                                "username"      => $result_set["username"],
                                "firstName"     => $result_set["first_name"],
                                "lastName"       => $result_set["last_name"],
                                "age"           => $result_set["age"]
                            ),
                            "code" => 200
                        ];
                    }else {
                        $result = [
                            "message" => "No user found"
                        ];
                    }
                    
                } else {
                    $result = [
                        "code"      => 405,
                        "message"   => "Please enter a valid type"
                    ];
                    header('HTTP/1.1 405', true, 405);
                }
            } else {
                $result = [
                    "code" => 405,
                    "message" => "Please enter an Id"
                ];
                header('HTTP/1.1 405', true, 405);
            }
        } else {
            $result = [
                "code" => 405,
                "message" => "Wrong method, use 'GET' instead"
            ];
            header('HTTP/1.1 405', true, 405);
        }
        $this->helper->returnJson($result);
    }

    public function connection(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = json_decode(file_get_contents('php://input'), true);

            if (count($data) == 2 
                && isset($data["username"]) && !is_null($data["username"]) && !empty($data["username"])
                && isset($data["password"]) && !is_null($data["password"]) && !empty($data["password"])
            ) {

                $userInfos = $this->model->connectUser($data["username"], $data["password"]);

                if ($userInfos) {

                    //C'est ici que la connexion est réussie
                    $this->helper->returnJson([
                        "data"  => array(
                            "id"            => $userInfos["id"],
                            "username"      => $userInfos["username"],
                            "firstName"     => $userInfos["first_name"],
                            "lastName"      => $userInfos["last_name"],
                            "age"           => $userInfos["age"]
                        ),
                        "code"  => 200
                    ]);

                }else {

                    $this->helper->returnJson([
                        "message" => "This username does not exist or Wrong combination of username/password"
                    ]);
                    header('HTTP/1.1 400', true, 400);

                }
            }else {

                $this->helper->returnJson([
                    "code"      => 401,
                    "message"   => "Missing fields"
                ]);
                header('HTTP/1.1 400', true, 400);

            }
        }else {

            $this->helper->returnJson([
                "message" => "Wrong method, please use 'POST' method instead"
            ]);
            header('HTTP/1.1 405', true, 405);

        }

    }
}
?>