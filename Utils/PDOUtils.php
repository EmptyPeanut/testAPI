<?php
declare(strict_types=1);
namespace Utils;
use \PDO;


class PDOUtils{

    private $SERVER;
    private $DB_NAME;
    private $USER;
    private $MDP;
    private static $connection;

    public function __construct()
    {
        $this->SERVER = $_ENV['DB_SERVER'];
        $this->DB_NAME = $_ENV['DB_NAME'];
        $this->USER = $_ENV['DB_USER'];
        $this->MDP = $_ENV['DB_PWD'];
    }
    public function getConnection(){
        
        if (is_null(self::$connection) || empty(self::$connection)) {
            try {
                self::$connection = new PDO(
                'mysql:host=' . $this->SERVER . ';dbname=' . $this->DB_NAME,
                $this->USER,
                $this->MDP, 
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
                );
            } catch (\Throwable $th) {
                self::$connection = null;
                throw $th;
            }
        }
        return self::$connection;
    }

    public function pdo($prepared_sql, $parameters_array, $select_query)
    {
        $result_set = null;

        $conn = $this->getConnection();

        // On récupère la connexion
        
        if (!is_null(self::$connection)) {
            try {
            // Preparation de la requête SQL
            $pdo_statement = $conn->prepare($prepared_sql);

            // Liage aux paramètres
            for ($i = 0; $i < count($parameters_array); $i++) {

                // Attention la numérotation des paramètres commence à 1 contrairement l'index du tableau qui commence à 0
                $pdo_statement->bindParam($i + 1, $parameters_array[$i]);
            }

            // Execution de la requête
            $pdo_statement->execute();

            // Vérification de l'existence de lignes dans le jeu de résultat dans l'unique cas d'une requête SELECT
            // ! https://stackoverflow.com/questions/55483165/fix-sqlstatehy000-general-error-when-using-fetch-after-insert?noredirect=1&lq=1
            if ($select_query && $pdo_statement->rowCount() > 0) {

                // Récupération et renvoi du jeu de résultats sous la forme d'un tableau associatif
                // ! Risque de dépassement des ressources en cas d'un "énorme" jeu de résultats
                $result_set = $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
            }
            } catch (\PDOException $e) {

                echo "Erreur général !: " . $e->getMessage() . " " . $e->getLine();
                die();
            }
        }
        
        return $result_set;
    }
}