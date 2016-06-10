<?php

namespace SocialHelper\Account;

class Account
{
    private $meta;
    private $db;
    public $twitterConnection;
    private $config;
    private $account_id;

    public function __construct($db = null, $config = null, $id = null)
    {
        $this->config = $config;
        $this->meta = null;
        $this->twitterConnection = null;
        $this->account_id = $id;
        $this->db = $db;
    }

    public function connectToTwitter()
    {
        if (isset($this->twitterConnection->connection_type) && 'user' == $this->twitterConnection->connection_type) {
            return true;
        }

        $this->twitterConnection = new \SocialHelper\Twitter\Twitter($this->config);

        if (null === $this->meta) {
            $response = $this->getMeta();

            if (!$response) {
                $error = new \SocialHelper\Error\Error(10);
                return $error;
            }

            if (is_error($response)) {
                return $response;
            }
        }

        if (!isset($this->meta['twitterToken']) || !isset($this->meta['twitterSecret'])) {
            $error = new \SocialHelper\Error\Error(11);
            return $error;
        }

        $key = $this->meta['twitterToken'];
        $secret = $this->meta['twitterSecret'];

        $response = $this->twitterConnection->setupUserConnection($key, $secret);

        if (!$response) {
            $error = new \SocialHelper\Error\Error(12);
            return $error;
        }

        if (is_error($response)) {
            return $response;
        }

        return true;
    }

    private function getMeta()
    {
        $meta = $this->db->getAccountMeta();

        if (!$meta) {
            return false;
        }

        if (is_error($meta)) {
            return $meta;
        }

        $this->meta = $meta;
        return true;
    }
}
