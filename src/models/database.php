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
            $this->config->database->database
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
        // $stmt->bind_param("s", $tag);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$res->num_rows) {
            return false;
        }

        $tracking_queries = array();

        while ($tracking_query = $res->fetch_assoc()) {
            $tracking_queries[] = $tracking_query;
        }

        return $tracking_queries;
    }
}
