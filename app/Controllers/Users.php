<?php
namespace App;
use Utils\PDOUtils;
// require_once './Utils/PDOUtils.php';


class Users {
    private $utils;
    function __construct(){
        $this->utils = new PDOUtils();
    }
    public function printMyText(string $myString){
        echo($myString);
    }
}
?>