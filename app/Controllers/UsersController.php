<?php
namespace App\Controllers;
use Utils\PDOUtils;
use App\Models\Users;
// require_once './Utils/PDOUtils.php';


class UsersController {
    private $utils;
    private $model;
    function __construct(){
        $this->utils = new PDOUtils();
        $this->model = new Users();
    }
    public function printMyText(string $myString){
        echo($myString);
    }
    
}
?>