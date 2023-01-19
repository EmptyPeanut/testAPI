<?php
namespace App\Models;
use Utils\PDOUtils;

class Users {
    private $utils;

    public function __construct(){
        $this->utils = new PDOUtils();
    }

    public function getAllUsers(){
        $result_set = $this->utils->pdo('SELECT * FROM authors', [], true);
        return $result_set;
    }
    public function addUser($username, $pwd){
        $result_set = $this->utils->pdo(
            'INSERT INTO authors (username, password) VALUES (?, ?)',
            [$username, $pwd],
            false
        );
    }
}