<?php
namespace App\Models;
use Utils\PDOUtils;
use App\Entities\User;

class UsersService {
    private $utils;

    public function __construct()
    {
        $this->utils = new PDOUtils();
    }

    /**
     * Get every existing users
     * @return array
     */
    public function getAllUsers(){
        $result_set = $this->utils->pdo('SELECT * FROM players', [], true);
        $result = array();
        foreach ($result_set as $data) {
            $user = new User();

            $user->setId($data['id']);
            $user->setUsername($data['username']);
            $user->setFirstName($data['first_name']);
            $user->setLastName($data['last_name']);
            $user->setAge($data['age']);
            $user->setPassword($data['password']);
            
            array_push($result, $user);
        }
        return $result;
    }

    /**
     * Get a user by the given Id
     * @param int $id
     * @return array
     */
    public function getUser(int $id){
        $result_set = $this->utils->pdo('SELECT * FROM players WHERE id = ?', [$id], true);

        $user = new User();
        $user->setId($result_set['id']);
        $user->setUsername($result_set['username']);
        $user->setFirstName($result_set['first_name']);
        $user->setLastName($result_set['last_name']);
        $user->setAge($result_set['age']);
        $user->setPassword($result_set['password']);

        return $user;
    }

    /**
     * Create a new user
     * @param string    $firstname
     * @param string    $lastname
     * @param string    $username
     * @param string    $pwd
     * @param bool|null $age
     * @return int $id
     */
    public function addUser(User $user){
        $this->utils->pdo(
            'INSERT INTO players (first_name, last_name, username, password, age) VALUES (?, ?, ?, ?, ?)',
            [$user->getFirstName(), $user->getLastName(), $user->getUsername(), $user->getPassword(), $user->getAge()],
            false
        );
        return $this->userExists($user->getUsername(), true)[0];

    }

    /**
     * Modify a user by the given Id
     * @param int $id
     * @return void
     */
    public function modifyUser(int $id){
        //TODO: à refaire, dépend de comment est fait l'envoi dans le front
        $myQuery = "";
        $this->utils->pdo(
            "UPDATE players SET nom_colonne_1 = 'nouvelle valeur' WHERE id = ?",
            [$id],
            false
        );
    }

    /**
     * Checks if a user exists by the given username
     * @param string $username
     * @param bool $return if true, returns user data [id, username, password]
     * @return bool|array
     */
    public function userExists(string $username, bool $return = false){
        $result_set = $this->utils->pdo('SELECT id, username, password FROM players WHERE username = ?', [$username], true);
        if (!is_null($result_set) && !empty($result_set)) {
            if ($return === false) {
                return true;
            }else {
                return $result_set;
            }
        }else {
            return false;
        }
    }

    //TODO: Avec JWT, renvoyer un bearer lorsque la conexion s'est bien faite. Dans l'autorisation se trouvera le pseudo ou l'id de l'utilisateur
    //qu'on pourra utiliser pour chaque action qu'il fera
    /**
     * Check if the user exists, then verify if it's the right password
     * @param string    $username
     * @param string    $pwd
     * @param bool|null $data
     * @return bool|array|null
     */
    public function connectUser(string $username, string $pwd, bool $data = false): bool|array|null
    {
        $user = $this->userExists($username, true);
        if ($user !== false) {
            if (!empty($user[0]["password"]) && password_verify($pwd, $user[0]["password"])) {
                if ($data === true) {
                    return $user[0];
                }else {
                    return true;
                } 
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
}