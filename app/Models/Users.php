<?php
namespace App\Models;
use Utils\PDOUtils;

class Users {
    private $utils;

    public function __construct(){
        $this->utils = new PDOUtils();
    }

    public function getAllUsers(){
        $result_set = $this->utils->pdo('SELECT * FROM players', [], true);
        return $result_set;
    }
    public function getUser(int $id)
    {
        $result_set = $this->utils->pdo('SELECT * FROM players WHERE id = ?', [$id], true);
        return $result_set;
    }
    public function addUser($username, $pwd){
        $this->utils->pdo(
            'INSERT INTO players (username, password) VALUES (?, ?)',
            [$username, $pwd],
            false
        );
    }

    public function modifyUser(int $id){
        //TODO: à refaire, dépend de comment est fait l'envoi dans le front
        $this->utils->pdo(
            "UPDATE players SET nom_colonne_1 = 'nouvelle valeur' WHERE id = ?",
            [$id],
            false
        );
    }
}