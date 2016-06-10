<?php

namespace SocialHelper\Twitter;

class Twitter
{
    private $config;
    private $consumer_key;
    private $consumer_secret;
    private $connection_type;
    private $connection;

    public function __construct($config = null)
    {
        $this->config = $config;
        $this->consumer_key = $config->twitter->key;
        $this->consumer_secret = $config->twitter->secret;
        $this->connection_type = null;
    }

    public function getObjectsByQuery($tracking_query)
    {
        $query = $tracking_query->getQuery();
        $query = parse_twitter_query($query);

        $twitter_search_array = array(
            'q' => $query['query'],
            "count" => 100,
            'exclude_replies' => true,
            'lang' => 'en',
            'result_type' => 'recent',
        );

        $tweet_response = $this->connection->get("search/tweets", $twitter_search_array);
        $objects = get_twitter_objects_by_reponse($tweet_response);

        return $objects;
    }

    public function setupAppConnection()
    {
        try {
            $this->connection = new \Abraham\TwitterOAuth\TwitterOAuth($this->consumer_key, $this->consumer_secret);
        } catch (Exception $e) {
            $this->connection = false;
        }

        $this->connection_type = 'app';
        return $this->connection;
    }

    public function verifySuccessfulConnection()
    {
        switch ($this->connection_type) {
            case 'user':
                return $this->verifyUserCredentials();
            case 'app':
                return false;
            default:
                return false;
        }
    }

    public function verifyUserCredentials()
    {
        if (null === $this->connection_type) {
            return false;
        }

        if ('user' != $this->connection_type) {
            return false;
        }

        $content = $this->connection->get("account/verify_credentials");

        if (isset($content->errors)) {
            return false;
        }

        return true;
    }

    public function setupUserConnection($token, $secret)
    {
        try {
            $this->connection = new \Abraham\TwitterOAuth\TwitterOAuth($this->consumer_key, $this->consumer_secret, $token, $secret);
        } catch (Exception $e) {
            $error = new \SocialHelper\Error\Error(13);
            return $error;
        }

        $this->connection_type = 'user';

        if (!$this->verifyUserCredentials()) {
            $error = new \SocialHelper\Error\Error(14);
            return $error;
        }
        
        return true;
    }

    public function getConnectionType()
    {
        return $this->connection_type;
    }
}
