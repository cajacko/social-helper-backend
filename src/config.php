<?php

namespace SocialHelper\Config;

class Config
{
    private $config;
    private $vars;

    public function __construct()
    {
        $config = file_get_contents('config.json', true);
        $this->config = json_decode($config);

        $vars = file_get_contents('vars.json', true);
        $this->vars = json_decode($vars);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getVars()
    {
        return $this->vars;
    }
}
