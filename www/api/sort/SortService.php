<?php
namespace api\sort;
require_once __DIR__."/../../autoload.php";

use api\Service;
use libs\ApiLib;
use libs\SortLib;

class SortService extends Service {

    // Surcharge Service.__construct() pour ajouter le traitement spécifique de la requête.
    public function __construct($allowed_verbs=["GET"])
    {
        $this->requiredParams = [
            "GET"=>["arr"]
        ];
        parent::__construct($allowed_verbs);
    }


    // Surcharge Service::SetParameters() pour y ajouter le nom de la fonction de tri à utiliser,
    // récupérée depuis l'URI de la requête.
    public function SetParameters(): void
    {
        parent::SetParameters();

        // Récupère le nom de l'endpoint (.../{endpoint}/index.php)
        preg_match("/^.*\/(?P<folder_name>.+)\/.+\.php$/", $_SERVER["PHP_SELF"], $matches);
        // Vérifie si une fonction du nom récupéré {endpoint} existe dans SortLib, sinon attribue 'false'
        $this->paramValues->sortFunc = $matches["folder_name"] ?? false;
    }

    // Renvoie l'erreur en réponse et termine le script si un paramètre est invalide.
    public function CheckParameters()
    {
        // La méthode associée à l'endpoint n'existe pas
        if (!method_exists(SortLib::class, $this->paramValues->sortFunc)) {
            ApiLib::WriteErrorResponse(500, "Aucune fonction de tri associée à `".$this->paramValues->sortFunc."`.");
        }
        // Le type du paramètre est invalide
        if (!is_array($this->paramValues->arr)) {
            ApiLib::WriteErrorResponse(400, "Le paramètre `arr` doit être de type array.");
        }
    }

    public function GET(){
        // Trie l'array avec la fonction associée à l'endpoint
        $sortedArr = SortLib::{$this->paramValues->sortFunc}($this->paramValues->arr);
        // Ecrit le json de la réponse et l'envoie
        ApiLib::WriteResponse(array("sort_function"=>$this->paramValues->sortFunc, "sorted_arr"=>$sortedArr));
    }

    public function POST(){}
    public function PATCH(){}
    public function DELETE(){}
}