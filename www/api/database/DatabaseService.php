<?php
namespace api\database;
use libs\ApiLib;


// Cette classe est divisée de DBRecordService à des fins de scalabilités, pour faciliter l'implémentation de nouveaux
// endpoints.
abstract class DatabaseService extends \api\Service
{
    // Surcharge Service.__construct() pour ajouter le traitement spécifique de la requête.
    protected $database;

    public function __construct($allowed_verbs=["GET"])
    {
        try {
            $this->database = new \database\Database();
        } catch (\PDOException $e) {
            // echo $e->getmessage();
            ApiLib::WriteErrorResponse(500, "Connexion impossible à la base de données.");
        }
        parent::__construct($allowed_verbs);
    }
}