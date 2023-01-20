<?php
namespace App;

class Helper{
    public function returnJson(array $data){
        header('content-type:application/json');
        echo(json_encode($data));
    }
}