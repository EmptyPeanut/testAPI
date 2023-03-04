<?php
declare(strict_types=1);
namespace App\Controllers;


use App\Helper;
use App\Models\UsersService;
use App\Entities\Users;
use \DateTimeImmutable;
use Firebase\JWT\JWT;


class UsersController {
    
    private $model;
    private $explodedURI;

    public function __construct(array $explodedUri){
        $this->explodedURI = $explodedUri;
        $this->model = new UsersService();
    }

    //TODO: Ajouter une autorisation Bearer spécifique pour les tâches "Admin", ou le spécifier dans le body du JWT
    //Ajouter la fonction qui check si admin ou pas dans le helper
    /**
     * Get data of every users in the database
     * @return JSON of every users
     */
    public function getAllUsers()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (Helper::checkAuthorization()) {
                $users     = $this->model->getAllUsers();
                $result         = [];
                $result['data'] = $users;

                // foreach ($users as $user) {

                    

                    // array_push(
                    //     $result['data'],
                    //     [
                    //         "id"            => $set["id"],
                    //         "username"      => $set["username"],
                    //         "firstName"     => $set["first_name"],
                    //         "lastName"      => $set["last_name"],
                    //         "age"           => $set["age"]
                    //     ]
                    // );
                // }

                if (!is_null($result)) {
                    $result['code'] = 200;
                    Helper::returnJson($result);
                }
            }
            

        }else {
            header('HTTP/1.1 405', true, 405);
        }
    }
    
    /**
     * Create a user from the data passed in the body request
     * @return Bearer
     */
    public function addUser(): void
    {
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

                            Helper::returnJson([
                                "code" => "200"
                            ]);
                            $this->model->addUser($data['firstName'], $data['lastName'], $data['username'], $hashedPwd, $data['age'] ?? null);

                        } catch (\Throwable $th) {

                            echo(json_encode([
                                "message" => "Something wrong happenned while adding your account to the database"
                            ]));

                        }
                        
                    } else {
                        
                        Helper::returnJson([
                            "code"      => 400,
                            "message"   => "Input of wrong type"
                        ]);

                        header('HTTP/1.1 400 Wrong type', true, 400);

                    }
                } else {

                    Helper::returnJson([
                        "code"      => 400,
                        "message"   => "There are missing fields"
                    ]);

                    header('HTTP/1.1 400 Username and password are not set', true, 400);

                }
            } else {

                Helper::returnJson([   
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
    public function modifyUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (count($this->explodedURI) == 2 && isset($this->explodedURI[1]) && !is_null($this->explodedURI[1]) && !empty($this->explodedURI[1])) {
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
     * @return JSON Response body
     */
    public function findOne()
    {
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
        Helper::returnJson($result);
    }

    /**
     * Check the username/password combination, if they match, returns the bearer authorization
     * @return Bearer
     */
    public function connection(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = json_decode(file_get_contents('php://input'), true);

            if (count($data) == 2 
                && isset($data["username"]) && !is_null($data["username"]) && !empty($data["username"])
                && isset($data["password"]) && !is_null($data["password"]) && !empty($data["password"])
            ) {

                $userInfos = $this->model->connectUser($data["username"], $data["password"], true);

                if ($userInfos) {

                    $token = Helper::createAuthorization($userInfos['username']);

                    //C'est ici que la connexion est réussie
                    if ($userInfos !== true && !empty($token)) {
                        header('Authorization: Bearer ' . $token);
                        return Helper::returnJson(["code"  => 200]);
                        
                    }
                    
                }else {

                    Helper::returnJson([
                        "message" => "This username does not exist or Wrong combination of username/password"
                    ]);
                    header('HTTP/1.1 400', true, 400);

                }
            }else {

                Helper::returnJson([
                    "code"      => 401,
                    "message"   => "Missing fields"
                ]);
                header('HTTP/1.1 400', true, 400);

            }
        }else {

            Helper::returnJson([
                "message" => "Wrong method, please use 'POST' method instead"
            ]);
            header('HTTP/1.1 405', true, 405);

        }

    }
}
?>