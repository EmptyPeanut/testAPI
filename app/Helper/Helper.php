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
        $data = "{$now->format('Y-m-d H:i:s')} : {$msg}\n";

        if ($type === 'error') {
            if (file_exists(__DIR__ . '/log/error_logs.txt')) {
                $stream = fopen(__DIR__ . '/log/error_logs.txt', 'a+');
                fwrite($stream, $data);
                fclose($stream);
            }
            
        }else{
            if (file_exists(__DIR__ . '/log/logs.txt')) {
                $stream = fopen(__DIR__ . '/log/logs.txt', 'a+');
                fwrite($stream, $data);
                fclose($stream);
            }
        }
    }

    /**
     * Returns data to JSON format
     * @param array $data
     */
    public static function returnJson(array $data){
        header('content-type:application/json');
        echo(json_encode($data));
    }

    /**
     * Check Bearer Token before doing an action
     * WIP -> Ajouter un check pour qu'un user puisse modifier seulement le compte avec lequel il est connecté
     * => Ajouter un paramètre par exemple de type (string)username qui est null de base et lorsque renseigné, check avec le
     * Username du token pour voir si c'est le même
     * 
     */
    public static function checkAuthorization(){
        
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {

            $auth = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            try {
                $jwt = JWT::decode($auth[1], new Key($_ENV['KEY'], 'HS512'));
                return true;
            } catch (\Throwable $th) {
                return static::returnJson([
                    "message" => "Wrong token"
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

    /**
     * Il faut absolument mettre l'id dans le token, rien que pour la modification d'utilisateur 
     * 
     * Create Bearer Token
     * @param string $username
     */
    public static function createAuthorization(string $username, int $id): string {
        
        $secretKey  = $_ENV['KEY'];
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+7 days')->getTimestamp();
        $serverName = "projectnumber2";

        $data = [
            'iat'       => $issuedAt->getTimestamp(),         // Issued at:  : heure à laquelle le jeton a été généré
            'iss'       => $serverName,                       // Émetteur
            'nbf'       => $issuedAt->getTimestamp(),         // Pas avant..
            'exp'       => $expire,                           // Expiration
            'username'  => $username,                         // Nom d'utilisateur
            'id'        => $id,                               // Id de l'utilisateur
        ];

        return JWT::encode($data, $secretKey,'HS512');

    }
}