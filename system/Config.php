<?php

class Config
{
    private $config;

    public function __construct()
    {
        $this->config = parse_ini_file('config.ini');
    }

    public function getData(){
        return $this->config;
    }
}