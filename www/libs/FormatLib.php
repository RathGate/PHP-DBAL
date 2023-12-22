<?php

namespace libs;

use Exception;

class FormatLib {

    static function KeyPair($arr=NULL, $format="%s", $sep=", "): string
    {
        if (!is_array($arr)) {
            // TODO
            throw new Exception("Not an array");
        }
        $result = "";
        for ($i = 0; $i < count($arr); $i++) {
            $result .= sprintf($format, $arr[$i]);
            if ($i != count($arr) - 1) {
                $result .= $sep;
            }
        }
        return $result;
    }
    static function isValidTypeOnly($arr, $valid_types=["integer", "string", "double"], $is_array=false): bool
    {
        if (!$is_array) {
            return in_array(gettype($arr), $valid_types);
        }
        foreach ($arr as $value) {
            if (!in_array(gettype($value), $valid_types)) {
                return false;
            }
        }
        return true;
    }

    static function ArrToStr($arr, $start="[", $end="]", $sep=", "): string
    {
        return "[".implode($sep, $arr)."]";
    }

    static function ArrayToInsert($arr) {
        if (!is_array($arr)) {
            return false;
        }
        $result = [];
        $i = 1;
        foreach ($arr as $key => $value) {
            $result[] = "$key = ?";
            $i++;
        }
        return implode(", ", $result);
    }
}