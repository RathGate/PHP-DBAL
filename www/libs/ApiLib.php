<?php

namespace libs;

class ApiLib
{

    // Ecrit et envoie le JSON d'une requête ayant provoqué une erreur.
    // Termine l'exécution du script si $exitWhenDone est true.
    static function WriteErrorResponse($code, $message, $exitWhenDone=true) {
        http_response_code($code);
        $response = array("error"=>array("code"=>$code, "message"=>$message));
        echo stripslashes(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        if ($exitWhenDone) {
            exit;
        }
    }

    // Ecrit et envoie le JSON d'une requête valide.
    // Termine l'exécution du script si $exitWhenDone est true.
    static function WriteResponse($data, $exitWhenDone=true) {
        header('Content-Type: application/json');
        $response = array("data"=>$data);
        echo stripslashes(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        if ($exitWhenDone) {
            exit;
        }
    }
}