<?php

namespace database;
require_once __DIR__."/../autoload.php";

use Exception;
use security\Credentials;
use libs\FormatLib;
use Throwable;
use Vtiful\Kernel\Format;

class Database
{
    private $connection;
    public static $valid_operators = [
        "=", "<>", "!=", "<", ">", "<=", ">=", "LIKE", "IN", "BETWEEN", "IS NULL", "IS NOT NULL",
    ];

    function __construct(Credentials $credentials = NULL)
    {
        $this->connection = new Connection($credentials);
    }

    function TableExists($table = NULL): bool
    {
        if (!isset($table) or $table == "") {
            throw new Exception("Le nom de table ne peut pas être vide.");
        }
        $cmd = 'SELECT table_name
            FROM information_schema.tables
            WHERE TABLE_SCHEMA = :dbname
            AND TABLE_NAME = :table;';
        $qry = $this->connection->dbh->prepare($cmd);
        $qry->bindValue(":dbname", $this->connection->dbname, \PDO::PARAM_STR);
        $qry->bindValue(":table", $table, \PDO::PARAM_STR);
        $qry->execute();
        return count($qry->fetchAll()) > 0;
    }

    /**
     * @throws Exception
     */
    function AddRecord($table, $record=NULL)
    {
        if (!$this->TableExists($table)) {
            throw new Exception("La table $table n'existe pas.");
        }
        $record = ["username"=>"RathGaate", "email"=>"marianne.corbel@ynov.com", "age"=>25];

        $cols = FormatLib::KeyPair(array_keys($record));
        $vals = FormatLib::KeyPair(range(0, count($record)-1), ":%s");
        $cmd = "INSERT INTO `$table` ($cols) VALUES ($vals);";
        $qry = $this->connection->dbh->prepare($cmd);

        $i = 0;
        foreach ($record as $col=>$val) {
            $qry->bindValue(":$i", $val);
            $i++;
        }
        $qry->execute();
        echo $this->connection->dbh->lastInsertId();
    }

    function DeleteRecord()
    {
    }

    function UpdateRecord()
    {
    }


    function SelectRecord($cols, $table=NULL, $where=NULL)
    {
        // Checks for thje existence of the table
        if (!$this->TableExists($table)) {
            throw new Exception("La table $table n'existe pas.");
        }

        // Column format
        if ($cols && !is_array($cols) && $cols != "*") {
            throw new Exception("Invalid column parameter");
        }
        if (!$cols || $cols == "*" || in_array("*", $cols)) {
            $cols = "*";
        } else {
            $cols = FormatLib::KeyPair($cols);
        }

        // Gestion de la clause WHERE
        if ($where) {
            $clause = Database::Where($where);
            $opt = " WHERE ".$clause["strReq"];
        } else {
            $opt = "";
        }

        $cmd = "SELECT $cols FROM $table$opt;";
        $qry = $this->connection->dbh->prepare($cmd);

        // Liaison des paramètres:
        if (isset($clause["values"]) and count($clause["values"])>0) {
            for ($i = 1; $i <= count($clause["values"]); $i++) {
                $qry->bindValue($i, $clause["values"][$i-1]);
            }
        }

        $qry->execute();
        echo $cmd;
        echo(json_encode($qry->fetchAll(\PDO::FETCH_ASSOC)));
//        echo $this->connection->dbh->lastInsertId();
    }

    static function Condition($val1, $operator, $val2, $val3=NULL): array
    {
        // Structure qui contiendra le résultat final
            // strReq : la string formattée [ex: "age BETWEEN ? AND ?]
            // values : les valeurs qui devront être bound dans PDO [ex: [12, 15]]
        $result = [
            "strReq" => "",
            "values" => []
        ];


        if (!in_array($operator, Database::$valid_operators)) {
            throw new DatabaseFormatException("Invalid operator `$operator`: supported comparison operators are "
                .FormatLib::ArrToStr(Database::$valid_operators, "[", "]", ", "));
        }
        if (!is_string($val1)) {
            //TODO
            throw new DatabaseFormatException("Invalid parameter format: comparison parameter 1 must be a string");
        }

        switch ($operator) {
            case "BETWEEN":
                // Error handling :
                if (!isset($val2) || !isset($val3) || !FormatLib::isValidTypeOnly($val2) || !FormatLib::isValidTypeOnly($val3)) {
                    throw new DatabaseFormatException("Invalid parameter format in `$operator` comparison: parameter 3 and 4 must 
                    both be set and of the following types: `string` or `integer/double` (here `".gettype($val2)."` and `".gettype($val3)."`).");
                }

                $result["strReq"] = "$val1 BETWEEN ? AND ?";
                $result["values"] = [$val2, $val3];
                break;
            case "IN":
                // Error handling :
                if (!$val2 || !is_array($val2) || !FormatLib::isValidTypeOnly($val2, true)) {
                    $comp = $val2 ? "" : "with no value";
                    throw new DatabaseFormatException("Invalid parameter format in `$operator` comparison: parameter 3 must
                    have a value and be of type `array` (here `".gettype($val2)."` $comp).");
                }

                $result["strReq"] = "$val1 IN (".join(", ", array_fill(0, count($val2), "?")).")";
                $result["values"] = $val2;
                break;
            case "IS NULL":
            case "IS NOT NULL" :
                $result["strReq"] = "$val1 $operator";
                $result["values"] = [];
                break;
            default:
                // Error handling :
                if (!FormatLib::isValidTypeOnly($val2)) {
                    throw new DatabaseFormatException("Invalid parameter format in `$operator` comparison: parameter 3 must
                    have a value of the following types: `string` or `integer/double` (here `".gettype($val2)."`).");
                }

                $result["strReq"] = "$val1 $operator ?";
                $result["values"] = [$val2];
        }
        return $result;
    }


    static function Where($conditionArr, $is_nested=false): array
    {
        // Structure qui contiendra le résultat final
        // strReq : la string formattée [ex: "username LIKE ? AND age BETWEEN ? AND ?]
        // values : les valeurs qui devront être bound dans PDO [ex: ["%Gate", 12, 15]]
        $result = [
            "strReq" => "",
            "values" => []
        ];

        if (!is_array($conditionArr)) {
            throw new DatabaseFormatException("Invalid format for WHERE clause : must be of type `array` (here `".gettype($conditionArr)."`).");
        }
        if (count($conditionArr) < 2) {
            throw new DatabaseFormatException("Invalid format for WHERE clause : array must contain at least 2 elements (column and operator, more if 
             aggregation or comparison) (here `".count($conditionArr)."`).");
        }

        $val1 = $conditionArr[0] ?? NULL;
        $operator = $conditionArr[1] ?? NULL;
        $val2 = $conditionArr[2] ?? NULL;
        $val3 = $conditionArr[3] ?? NULL;

        if ($operator == "AND" || $operator == "OR") {
            if (!$val2) {
                throw new DatabaseFormatException("Invalid parameter format in `$operator` aggregation: parameter 3 must be set and not empty.");
            }
            $leftTerm = Database::Where($val1, true);
            $rightTerm = Database::Where($val2, true);
            $temp = $leftTerm["strReq"]." $operator ".$rightTerm["strReq"];

            $result["strReq"] = $is_nested ? "($temp)" : $temp;

            $result["values"] = array_merge($result["values"], $leftTerm["values"]);
            $result["values"] = array_merge($result["values"], $rightTerm["values"]);

            return $result;
        }
        return Database::Condition($val1, $operator, $val2, $val3);
    }

}

class DatabaseFormatException extends Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}