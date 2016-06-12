<?php

namespace SocialHelper\Database;

class Database
{
    private $conn;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        $db = new \mysqli(
            $this->config->database->host,
            $this->config->database->user,
            $this->config->database->password,
            $this->config->database->database,
            $this->config->database->port,
            $this->config->database->socket
        );
        
        $db->set_charset('utf8mb4');
        $this->conn = $db;

    }

    public function getTrackingQueries()
    {
        $query = '
            SELECT *
            FROM trackingQueriesView
        ;';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            return false;
        }

        $tracking_queries = array();

        while ($tracking_query = $res->fetch_assoc()) {
            $tracking_queries[] = $tracking_query;
        }

        return $tracking_queries;
    }

    public function getTrackingQueryAccounts($tracking_query_ID = null)
    {
        return array(array('id' => 12));
    }

    public function getAccountMeta()
    {
        return array(
            'twitterToken' => 'xxx',
            'twitterSecret' => 'xxx'
        );
    }

    public function getObjectByUIDandType($UID, $type)
    {
        $query = '
            SELECT objects.objectID as ID
            FROM objects
            INNER JOIN objectTypes
                ON objectTypes.objectTypesID = objects.objectTypeID
            WHERE objects.UID = ? AND objectTypes.type = ?
        ;';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $UID, $type);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            return false;
        }

        if ($res->num_rows > 1) {
            $error = new \SocialHelper\Error\Error(16);
            return $error;
        }

        $object = $res->fetch_assoc();

        if (!is_array($object)) {
            $error = new \SocialHelper\Error\Error(17);
            return $error;
        }

        if (!isset($object['ID'])) {
            $error = new \SocialHelper\Error\Error(18);
            return $error;
        }

        if (!is_numeric($object['ID'])) {
            $error = new \SocialHelper\Error\Error(19);
            return $error;
        }
        
        return $object['ID'];
    }

    public function startTransaction()
    {
        $this->conn->autocommit(false);
        $this->conn->begin_transaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollback()
    {
        $this->conn->rollback();
    }


    public function query($query)
    {
        return $this->conn->query($query);
    }

    public function getKeywordType($type)
    {
        $query = '
            SELECT keywordTypesID as ID
            FROM keywordTypes
            WHERE type = ?
        ;';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            return false;
        }

        if ($res->num_rows > 1) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        $object = $res->fetch_assoc();

        if (!is_array($object)) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        if (!isset($object['ID'])) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        if (!is_numeric($object['ID'])) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }
        
        return $object['ID'];
    }
}
