<?php

namespace SocialHelper\Config;

class Config
{
    private $config;

    public function __construct()
    {
        $config = file_get_contents('config.json', true);
        $this->config = json_decode($config);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
