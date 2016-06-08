<?php

namespace SocialHelper\Twitter;

class Twitter
{
    private $config;
    private $key;
    private $secret;
    private $connection_type;

    public function __construct($config = null)
    {
        // $this->config = $config;
        // $this->key = $config->twitter->key;
        // $this->secret = $config->twitter->secret;
        // $this->setTwitterAppConnection();
        $this->connection_type = null;
    }

    public function getObjectsByQuery($query)
    {

    }

    public function setupAppConnection()
    {
        try {
            $this->app_connection = new \Abraham\TwitterOAuth\TwitterOAuth($this->key, $this->secret);
        } catch (Exception $e) {
            $this->app_connection = false;
        }

        return $this->app_connection;
    }

    public function setupUserConnection()
    {
        
    }

    public function getConnectionType()
    {
        return $this->connection_type;
    }
}
