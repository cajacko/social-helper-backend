<?php

namespace SocialHelper\Account;

class Account
{
    private $meta;
    private $db;
    public $twitterConnection;
    private $config;
    private $account_id;
    private $UID;
    private $type;

    public function __construct($db = null, $config = null, $id = null, $type = null, $UID = null)
    {
        $this->config = $config;
        $this->meta = null;
        $this->twitterConnection = null;
        $this->account_id = $id;
        $this->db = $db;
        $this->setType($type);
    }

    private function setUID($UID = null)
    {
        if (null == $UID) {
            // Get from db
            $this->UID = $UID;
        } else {
            $this->UID = $UID;
        }
    }

    private function setType($type = null)
    {
        if (null == $type) {
            // Get from db
            $this->type = $type;
        } else {
            $this->type = $type;
        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUID()
    {
        return $this->UID;
    }

    public function getID()
    {
        if (null == $this->account_id) {
            // TODO: get accountID
        }

        return $this->account_id;
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
        $meta = $this->db->getAccountMeta($this->account_id);

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
