<?php
namespace App\Models;
use Utils\PDOUtils;

class Users {
    private $utils;

    public function __construct(){
        $this->utils = new PDOUtils();
    }

    /**
     * Get every existing users
     * @return array
     */
    public function getAllUsers(){
        $result_set = $this->utils->pdo('SELECT * FROM players', [], true);
        return $result_set;
    }

    /**
     * Get a user by the given Id
     * @param int $id
     * @return array
     */
    public function getUser(int $id){
        $result_set = $this->utils->pdo('SELECT * FROM players WHERE id = ?', [$id], true);
        return $result_set;
    }

    /**
     * Create a new user
     * @param string    $firstname
     * @param string    $lastname
     * @param string    $username
     * @param string    $pwd
     * @param bool|null $age
     * @return void
     */
    public function addUser(string $firstname, string $lastname, string $username, string $pwd, string $age = null){
        $this->utils->pdo(
            'INSERT INTO players (first_name, last_name, username, password, age) VALUES (?, ?, ?, ?, ?)',
            [$firstname, $lastname, $username, $pwd, $age],
            false
        );
    }

    /**
     * Modify a user by the given Id
     * @param int $id
     * @return void
     */
    public function modifyUser(int $id){
        //TODO: à refaire, dépend de comment est fait l'envoi dans le front
        $this->utils->pdo(
            "UPDATE players SET nom_colonne_1 = 'nouvelle valeur' WHERE id = ?",
            [$id],
            false
        );
    }

    /**
     * Checks if a user exists by the given username
     * @param string $username
     * @return bool
     */
    public function userExists(string $username){
        $result_set = $this->utils->pdo('SELECT * FROM players WHERE username = ?', [$username], true);
        if (!is_null($result_set) && !empty($result_set)) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * Check if the user exists, then verify if it's the good password
     * @param string $username
     * @param string $pwd
     */
    public function connectUser(string $username, string $pwd, bool $data = false){
        if ($this->userExists($username)) {
            $result_set = $this->utils->pdo(
                'SELECT * FROM players WHERE username = ?',
                [$username],
                true
            )[0];
            // return var_dump($result_set);
            if (isset($result_set["password"]) && password_verify($pwd, $result_set["password"])) {
                if ($data === true) {
                    return $result_set;
                }else {
                    return true;
                }
                
            }else {
                return false;
            }
            //Voir comment on ouvre une 'session' sur React Native
        }else {
            return false;
        }
    }
}