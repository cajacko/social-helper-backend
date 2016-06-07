<?php

namespace SocialHelper\database;

class Database
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        $db = new \mysqli(
            $this->config->database->host,
            $this->config->database->user,
            $this->config->database->password,
            $this->config->database->database
        );

        $db->set_charset('utf8mb4');

        return $db;
    }
}
