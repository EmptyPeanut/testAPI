<?php
declare(strict_types=1);
namespace App\Controllers;


use App\Helper;
use App\Models\UsersService;
use App\Entities\User;
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
                $users          = $this->model->getAllUsers();
                $result['data'] = $users;
                // Helper::log('Une requête pour récuperer tous les users a eu lieu');

                if (!is_null($users)) {
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
    public function addUser()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {


            if (!empty($_POST['username'])
                && !empty($_POST['firstName'])
                && !empty($_POST['lastName'])
                && !empty($_POST['password'])) 
            {
                    
                $hashedPwd = password_hash($_POST["password"], PASSWORD_DEFAULT);

                try {
                    $user = new User();
                    
                    $user->setUsername($_POST['username']);
                    $user->setFirstName($_POST['firstName']);
                    $user->setLastName($_POST['lastName']);
                    !empty($_POST['age']) ? $user->setAge($_POST['age']) : $user->setAge(null);
                    $user->setPassword($hashedPwd);
                    
                    $userInfos = $this->model->addUser($user);
                    // die(var_dump($userInfos));
                    if ($userInfos !== false) {
                        $token = Helper::createAuthorization($userInfos['username'], $userInfos['id']);
                        header('Authorization: Bearer ' . $token);
                        Helper::log("Un user a été ajouté: {$user->getUsername()}");
                    }
                    
                    Helper::returnJson([
                        "code" => 200
                    ]);
                } catch (\Throwable $th) {

                    echo(json_encode([
                        "code" => 500,
                        "message" => "Something wrong happenned while adding your account to the database: {$th}"
                    ]));

                }
                    
            } else {

                Helper::returnJson([
                    "code"      => 400,
                    "message"   => "There are missing fields"
                ]);

                header('HTTP/1.1 400 Bad request', true, 400);

            }
            
        }else {
            header('HTTP/1.1 405', true, 405);
        }  
    }

    //TODO: Fonction incmplète ici et dans le model, ne sait pas encore comment la modification se fera
    public function modifyUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH' && strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {

            if (count($this->explodedURI) == 3 && !empty($this->explodedURI[3])) {
                $userId = (int)$this->explodedURI[3];
                if (!empty($userId)) {
                    $user = new User();
                    foreach ($_POST as $k => $val) {
                        match ($k) {
                            'username'      => $user->setUsername($val),
                            'firstName'     => $user->setFirstName($val),
                            'lastName'      => $user->setLastName($val),
                            'password'      => $user->setPassword(password_hash($val, PASSWORD_DEFAULT)),
                            'age'           => $user->setAge($val)
                        };
                    }
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {


            if (count($_POST) == 2 
                && !empty($_POST["username"])
                && !empty($_POST["password"])
            ) {

                $userInfos = $this->model->connectUser($_POST["username"], $_POST["password"], true);
                if ($userInfos !== false) {

                    $token = Helper::createAuthorization($userInfos['username'], $userInfos['id']);

                    //C'est ici que la connexion est réussie
                    if ($userInfos !== true && !empty($token)) {
                        
                        header('Authorization: Bearer ' . $token);
                        return Helper::returnJson(["code"  => 200]);
                        
                    }
                    
                }else {

                    Helper::returnJson([
                        "code"      => 400,
                        "message"   => "This username does not exist or Wrong combination of username/password"
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
                "code"      => 400,
                "message"   => "Wrong method, please use 'POST' method instead and content type of 'application/x-www-form-urlencoded'"
            ]);
            header('HTTP/1.1 405', true, 405);

        }

    }
}
?>