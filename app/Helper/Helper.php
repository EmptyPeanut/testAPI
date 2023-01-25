<?php
namespace App;
use \DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Helper{


    public static function returnJson(array $data){
        header('content-type:application/json');
        echo(json_encode($data));
    }

    public static function checkAuthorization(){
        
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {

            $auth = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            try {
                $jwt = JWT::decode($auth[1], new Key($_ENV['KEY'], 'HS512'));
                return true;
            } catch (\Throwable $th) {
                return static::returnJson([
                    "message" => "No token found or wrong token"
                ]);
                die();
            }

        }else {
            header('HTTP/1.0 400 Bad Request', true, 400);
            return static::returnJson([
                "message" => "No token found"
            ]);
            die();
        }

    }
}