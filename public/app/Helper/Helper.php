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
    public static function log(string $msg): void{

        $now = new DateTime('now');
        $data = "{$now->format('Y-m-d H:i:s')} : {$msg}\n";

        if (file_exists('../log/error.log')) {
            file_put_contents('../log/error.log', $data, FILE_APPEND);
        }
        
    }

    /**
     * Log either in the error_logs.csv or logs.csv file
     * @param string $type 'error' -> log dans le fichier d'erreur
     */
    public static function errorLog($errno, $errstr, $errfile, $errline){
        $now = new DateTime('now');
        $error_message = "[{$now->format('Y-m-d H:i:s')}] Erreur [{$errno}] : \"{$errstr}\" dans le fichier {$errfile} à la ligne {$errline}\n";

        if (file_exists('../log/error.log')) {
            file_put_contents('../log/error.log', $error_message, FILE_APPEND);
        }
        // http_response_code(500);
        // die(static::returnJson([
        //     "code" => 500,
        //     "message" => $error_message
        // ]));

    }
    /**
     * Log either in the error_logs.csv or logs.csv file
     * @param string $type 'error' -> log dans le fichier d'erreur
     */
    public static function exceptionLog(\Throwable $exception){
        $now = new DateTime('now');
        $error_message = "[{$now->format('Y-m-d H:i:s')}] Erreur {$exception->getCode()}: \"{$exception->getMessage()}\" dans le fichier {$exception->getFile()} à la ligne {$exception->getLine()}\n";

        if (file_exists('../log/error.log')) {
            file_put_contents('../log/error.log', $error_message, FILE_APPEND);
        }
        http_response_code(500);
        die(static::returnJson([
            "code" => 500,
            "message" => $error_message
        ]));

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
     * @param bool $return returns user's Id if true
     */
    public static function checkAuthorization(bool $return = false): bool|array|int
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {

            $auth = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            try {
                $jwt = JWT::decode($auth[1], new Key($_ENV['KEY'], 'HS512'));
                if ($return) {
                    return !empty($jwt->id) ? $jwt->id : static::returnJson([
                        "code"      => 403,
                        "message"   => "No id found in token"
                    ]);
                }else {
                    return true;
                }
            } catch (\Throwable $th) {
                return static::returnJson([
                    "code"      => 403,
                    "message"   => "Wrong token"
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