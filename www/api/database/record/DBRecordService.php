<?php
namespace api\database\record;

require_once __DIR__."/../../../autoload.php";
use api\database\DatabaseService;
use database\DatabaseFormatException;
use libs\ApiLib;

class DBRecordService extends DatabaseService {

    // Surcharge Service.__construct() pour ajouter le traitement spécifique de la requête.
    public function __construct($allowed_verbs=["GET"])
    {
        $this->requiredParams = [
            "GET"=>["table"],
            "POST"=>["table", "values"],
            "PATCH"=>["columns", "table", "values"],
            "DELETE"=>["table"]
        ];
        $this->optionParams = [
            "GET"=>["columns", "where", "order_by", "limit", "offset"],
            "POST"=>[],
            "PATCH"=>["where"],
            "DELETE"=>["where"]
        ];

        parent::__construct($allowed_verbs);;
    }

    // Renvoie l'erreur en réponse et termine le script si un paramètre est invalide.
    public function CheckParameters()
    {

    }

    public function GET()
    {
        if (!$this->database->TableExists($this->paramValues->table)) {
            ApiLib::WriteErrorResponse(400, "La table ".$this->paramValues->table." n'existe pas dans la base.");
        }

        try {
            $this->database->SelectRecord($this->paramValues->columns, $this->paramValues->table, $this->paramValues->where);
        } catch (DatabaseFormatException $e) {
            echo $e->getMessage();
        }


    }
    public function POST(){
        if (!$this->database->TableExists($this->paramValues->table)) {
            ApiLib::WriteErrorResponse(400, "La table ".$this->paramValues->table." n'existe pas dans la base.");
        }
//        $this->database->AddRecord($this->paramValues->table, $this->paramValues->values);

    }
    public function PATCH(){
        echo "hello from PATCH!";
        var_dump($this->requiredParams[$this->method] ?? "no parameter.");
    }
    public function DELETE(){
        echo "hello from DELETE!";
        var_dump($this->requiredParams[$this->method] ?? "no parameter.");
    }
}