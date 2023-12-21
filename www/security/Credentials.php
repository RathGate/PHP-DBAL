<?php

namespace security;

class Credentials
{
    public $service_name;

    function __construct(?string $service_name="")
    {
        $this::SetParameters();
    }

    public function SetParameters():void {
        if (!isset($service_name) || $service_name == "") {
            $this->service_name = "database";
        } else {
            $this->service_name = $service_name;
        }

        $f = Files::GetSecureFile("credentials/".$this->service_name.".json");
        $json_data = json_decode($f);
        foreach ($json_data as $key => $value) {
            $this->$key = $value;
        }
    }
}