<?php

namespace api;
require_once __DIR__."/../autoload.php";
use libs\ApiLib;

abstract class Service {
    protected $allowedVerbs = [];
    protected $requiredParams = [];
    protected $optionParams = [];
    protected $paramValues;
    protected $method;

    public function __construct($allowed_verbs=["GET"])
    {
        $this->allowedVerbs = array_change_key_case($allowed_verbs, CASE_UPPER);
        $this->method = strtoupper($_SERVER["REQUEST_METHOD"]);

        // Vérifie le verbe de la requête
        if (!self::IsValidMethod()) {
            ApiLib::WriteErrorResponse(405, "Méthode ".$this->method." non autorisée.");
        }

        // Récupère, traite et vérifie les paramètres
        $this::SetParameters();
        // Todo: séparé de SetParameters mais peut certainement être factorisé en une fonction,
        // Todo: mais je ne pense pas savoir comment faire.
        $this->CheckParameters();

        // Si aucune erreur n'a été détectée, lance l'exécution du service en lui-même.
        $this->Trig();
    }

    public function Trig() {
        $fct = $this->method;
        $this->$fct();
    }

    // Retourne true si la REQUEST_METHOD est présente dans les allowedMethods
    public function IsValidMethod(): bool
    {
        return in_array($this->method, $this->allowedVerbs);
    }

    // Enregistre les paramètres dans l'object $this->params.
    public function SetParameters(): void {
        $this->paramValues = new \stdClass();
        $this->requiredParams[$this->method] = $this->requiredParams[$this->method] ?? [];

        $rawParamValues = [];
        switch ($this->method) {
            case "PATCH":
            case "PUT":
                parse_str(file_get_contents('php://input'), $rawParamValues);
                break;
            case "POST":
                $rawParamValues = $_POST;
                break;
            default:
                $rawParamValues = $_GET;
        }
        if ($this->method == "PATCH" || $this->method == "PUT") {

        }
//        else if ($this->method == "GET" || $this->method == "DELETE") {
//            $rawParamValues = $_GET;
//        } else {
//            $rawParamValues = $_POST;
//        }
        foreach ($this->requiredParams[$this->method] as $param) {

            if (!isset($rawParamValues[$param])) {
                ApiLib::WriteErrorResponse(400, "Paramètre obligatoire `".$param."` manquant.");
            }
            try {
                $this->paramValues->$param = json_decode($rawParamValues[$param], false, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                ApiLib::WriteErrorResponse(400, "Erreur de syntaxe: impossible de parse le paramètre `".$param."` [format JSON attendu].");
                return;
            }
        }

        // ---------------------
        $this->optionParams[$this->method] = $this->optionParams[$this->method] ?? [];

        foreach ($this->optionParams[$this->method] as $param) {
            if (isset($rawParamValues[$param])) {
                try {
                    $this->paramValues->$param = json_decode($rawParamValues[$param], false, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    ApiLib::WriteErrorResponse(400, "Erreur de syntaxe: impossible de parse le paramètre `".$param."` [format JSON attendu].");
                    return;
                }
            } else {
                $this->paramValues->$param = "";
            }
        }
    }

    // Fonction à déclarer dans les classes enfant pour vérifier les paramètres et valeurs
    // spécifiques au service en question.
    public abstract function CheckParameters();

    public abstract function GET();
    public abstract function POST();
    public abstract function PATCH();
    public abstract function DELETE();
}

