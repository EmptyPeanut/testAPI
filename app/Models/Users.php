<?php
namespace App\Models;
use Utils\PDOUtils;

class Users {
    private $utils;

    public function __construct(){
        $this->utils = new PDOUtils();
    }

    public function getAllUsers(){
        $this->utils->pdo('SELECT * FROM authors', [], true);
    }
}