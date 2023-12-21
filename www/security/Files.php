<?php

namespace security;

class Files
{
    public static function GetPath(string $pathFromRoot=""): string {
        return $_SERVER["DOCUMENT_ROOT"].$pathFromRoot;
    }

    public static function GetSecurePath(string $pathFromSecureRoot=""): string {
        return $_SERVER["DOCUMENT_ROOT"]."/../".$pathFromSecureRoot;
    }

    public static function GetFile(string $pathFromRoot="")
    {
        return file_get_contents(self::GetPath($pathFromRoot));
    }

    public static function GetSecureFile(string $pathFromSecureRoot="")
    {
        return file_get_contents(self::GetSecurePath($pathFromSecureRoot));
    }
}