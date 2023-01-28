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

    public static function createAuthorization($userInfos){
        $secretKey  = $_ENV['KEY'];
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+7 days')->getTimestamp();
        $serverName = "projectnumber2";
        $username   = $userInfos["username"];

        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at:  : heure à laquelle le jeton a été généré
            'iss'  => $serverName,                       // Émetteur
            'nbf'  => $issuedAt->getTimestamp(),         // Pas avant..
            'exp'  => $expire,                           // Expiration
            'userName' => $username,                     // Nom d'utilisateur
        ];

        $token = JWT::encode($data, $secretKey,'HS512');
        if (!is_null($token) && !empty($token)) {
            return $token;
        }else {
            static::returnJson([
                "message" => "Something wrong happenned creating the token"
            ]);
        }
    }

    public static function checkAuthorization(){
        //TODO: ajouter un bool en param pour dire si c'est censé être un admin ou non, en fonction du param on check le role dans le token
        
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {

            $auth = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            try {
                $jwt = JWT::decode($auth[1], new Key($_ENV['KEY'], 'HS512'));
                //TODO: Faire la vérif dans le temps et return le user
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