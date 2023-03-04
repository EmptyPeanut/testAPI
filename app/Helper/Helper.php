<?php
namespace App;

use DateTime;
use \DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Helper{


    /**
     * Log either in the error_logs.csv or logs.csv file
     * @param string $type 'error' -> log dans le fichier d'erreur
     */
    public static function log(string $msg, string $type = null): void{
        $now = new DateTime('now');
        $data = [$now, $msg];
        if ($type === 'error') {
            $stream = fopen('../log/error_logs.txt', 'a+');
            fputcsv($stream, $data);
            fclose($stream);

        }else{
            $stream = fopen('../log/logs.txt', 'a+');
            fputcsv($stream, $data);
            fclose($stream);

        }
    }

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
                    //Test1
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


    public static function createAuthorization($username){
        
        $secretKey  = $_ENV['KEY'];
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+7 days')->getTimestamp();
        $serverName = "projectnumber2";

        $data = [
            'iat'       => $issuedAt->getTimestamp(),         // Issued at:  : heure à laquelle le jeton a été généré
            'iss'       => $serverName,                       // Émetteur
            'nbf'       => $issuedAt->getTimestamp(),         // Pas avant..
            'exp'       => $expire,                           // Expiration
            'userName'  => $username,                     // Nom d'utilisateur
        ];

        return JWT::encode($data, $secretKey,'HS512');

    }
}