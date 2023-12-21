<?php
//
//use libs\FormatLib;
//
use libs\FormatLib;

require_once __DIR__."/autoload.php";


function isValidTypeOnly($arr, $valid_types=["integer", "string", "double"]): bool
{
    if (!is_array($arr)) {
        return in_array(gettype($arr), $valid_types);
    }
    foreach ($arr as $value) {
        if (!in_array(gettype($value), $valid_types)) {
            return false;
        }
    }
    return true;
}

/**
 * @throws Exception
 */
function Condition($val1, $operator, $val2, $val3=NULL): array
{
    $result = [
        "strReq" => "",
        "values" => []
    ];
    $valid_operators = [
        "=", "<>", "!=", "<", ">", "<=", ">=", "LIKE", "IN", "BETWEEN", "IS NULL", "IS NOT NULL",
    ];

    if (!in_array($operator, $valid_operators)) {
        throw new Exception("Invalid operator");
    }
    if (!is_string($val1)) {
        throw new Exception("Invalid parameter 1 must be a string");
    }

    switch ($operator) {
        case "BETWEEN":
            if (!isset($val2) || !isset($val3) || !isValidTypeOnly($val2) || !isValidTypeOnly($val3)) {
                throw new Exception("Invalid param 2 and 3 must be set and both a number or a string");
            }

            $result["strReq"] = "$val1 BETWEEN ? AND ?";
            $result["values"] = [$val2, $val3];
            break;
        case "IN":
            if (!is_array($val2) || !isValidTypeOnly($val2, true)) {
                throw new Exception("Invalid parameter 3 must be an array");
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
            if (!isValidTypeOnly($val2)) {
                throw new Exception("Invalid parameter 3 must be a number or a string");
            }

            $result["strReq"] = "$val1 $operator ?";
            $result["values"] = [$val2];
    }
    return $result;
}

/**
 * @throws Exception
 */
function Where($conditionArr, $is_nested=false): array
{
    $result = [
        "strReq" => "",
        "values" => []
    ];

    $valid_operators = [
        "=", "<>", "!=", "<", ">", "<=", ">=", "LIKE", "IN", "BETWEEN", "IS NULL", "IS NOT NULL",
    ];
    if (!is_array($conditionArr)) {
        throw new Exception("Not an array");
    }
    if (count($conditionArr) < 2) {
    throw new Exception("Two few arguments for WHERE clause");
}

    $val1 = $conditionArr[0] ?? NULL;
    $operator = $conditionArr[1] ?? NULL;
    $val2 = $conditionArr[2] ?? NULL;
    $val3 = $conditionArr[3] ?? NULL;

    if ($operator == "AND" || $operator == "OR") {
        $leftTerm = Where($val1, true);
        $rightTerm = Where($val2, true);
        $temp = $leftTerm["strReq"]." $operator ".$rightTerm["strReq"];

        $result["strReq"] = $is_nested ? "($temp)" : $temp;
        echo gettype($leftTerm["values"]);
        $result["values"] = array_merge($result["values"], $leftTerm["values"]);
        $result["values"] = array_merge($result["values"], $rightTerm["values"]);
        return $result;
    }
    return Condition($val1, $operator, $val2, $val3);
}

$example = [["id","=",2],"OR",[["username","IN",["oui", "bonjour"]],"AND",["age",">=",24]]];
