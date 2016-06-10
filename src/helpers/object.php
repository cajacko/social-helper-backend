<?php

namespace SocialHelper\Object;

class SocialObject
{
    private $config;
    private $db;
    private $type;
    private $meta;
    private $UID;
    private $data;
    private $save_fields;
    private $ID;
    private $tracking_query;

    public function __construct($config, $db)
    {
        $this->config = $config;
        $this->db = $db;
        $this->UID = null;
        $this->meta = null;
        $this->type = null;
        $this->data = null;
        $this->ID = null;
        $this->tracking_query = null;
        $this->insert_update = null;
        $this->save_fields = array();
    }

    public function getID()
    {
        if (null === $this->UID) {
            return false;
        }

        $response = $this->db->getObjectByUIDandType($this->UID, $this->type);

        if (false === $response) {
            return false;
        }

        if (is_error($response)) {
            return $response;
        }

        if (!is_numeric($response)) {
            $error = new \SocialHelper\Error\Error(26);
            return $error;
        }

        $this->ID = $response;
        return $response;
    }

    public function defineTweet($tweet)
    {
        if (!isset($tweet->id)) {
            return false;
        }

        $this->UID = $tweet->id;
        $this->data = $tweet;
        $this->type = 'tweet';
    }

    public function saveNew()
    {
        $this->save_fields[] = 'insert';
        return true;
    }

    public function saveExisting()
    {
        if (null == $this->ID) {
            $error = new \SocialHelper\Error\Error(27);
            return $error;
        }

        $this->save_fields[] = 'update';
        return true;
    }

    public function saveMeta()
    {
        // $this->save_fields[] = 'meta';
        // TODO: Actually parse the meta
        return true;
    }

    public function save()
    {
        $save_fields = array_unique($this->save_fields);

        if (in_array('update', $save_fields) && in_array('insert', $save_fields)) {
            $error = new \SocialHelper\Error\Error(28);
            return $error;
        }

        if (null === $this->type) {
            $error = new \SocialHelper\Error\Error(29);
            return $error;
        }

        $this->db->startTransaction();
        $date = date('Y-m-d H:i:s');

        if (in_array('insert', $save_fields)) {
            $query = "SELECT @objectTypeID := objectTypesID FROM objectTypes WHERE type = '" . $this->type . "';";
            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(31);
                return $error;
            }

            $query = "INSERT INTO objects (UID, objectTypeID, dateAdded, dateUpdated)";
            $query .= "VALUES('" . $this->UID . "', @objectTypeID, '" . $date . "', '" . $date ."');";
            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(32);
                return $error;
            }

            $query = "SET @objectID = LAST_INSERT_ID();";
            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(33);
                return $error;
            }
        } elseif (in_array('update', $save_fields) && is_numeric($this->ID)) {
            $query = "SET @objectID = " . $this->ID .";";
            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(34);
                return $error;
            }
        } else {
            $error = new \SocialHelper\Error\Error(35);
            return $error;
        }

        if (in_array('trackingQuery', $save_fields)) {
            $tracking_query_id = $this->tracking_query->getID();

            if (!is_numeric($tracking_query_id)) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(37);
                return $error;
            }

            $query = "INSERT INTO objectTrackingQuery (objectID, trackingQueryID, dateAdded, dateUpdated)";
            $query .= "VALUES(@objectID, " . $tracking_query_id . ", '" . $date . "', '" . $date ."');";
            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(36);
                return $error;
            }
        }

        $this->db->commit();
        return true;
    }

    public function addTrackingQuery($tracking_query)
    {
        $this->save_fields[] = 'trackingQuery';
        $this->tracking_query = $tracking_query;
        return true;
    }
}
