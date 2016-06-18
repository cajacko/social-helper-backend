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
    private $keywords;
    private $action;
    private $account;

    public function __construct($config, $db)
    {
        $this->config = $config;
        $this->db = $db;
        $this->UID = null;
        $this->meta = null;
        $this->type = null;
        $this->data = null;
        $this->keywords = null;
        $this->account = null;
        $this->ID = null;
        $this->action = null;
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

        return true;
    }

    public function defineTwitterUser($user)
    {
        if (!isset($user->id)) {
            return false;
        }

        $this->UID = $user->id;
        $this->data = $user;
        $this->type = 'twitterUser';
        return true;
    }

    public function defineLink($link)
    {
        $this->data = new \SocialHelper\Link\Link();
        $response = $this->data->setSourceUrl($link);
        $url = $this->data->resolveUrl();

        $this->UID = $url;
        $this->type = 'link';

        return true;
    }

    public function addAccountAction($account)
    {
        if (!is_account($account)) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        $this->account = $account;

        switch ($this->type) {
            case 'tweet':
                $this->action = 'tweet';
                break;
            case 'link':
                $this->action = 'tweet';
                break;
            default:
                return false;
        }

        $this->save_fields[] = 'accountAction';       

        return true;
    }

    private function saveReferences()
    {
        if (isset($this->data->user)) {
            $reference = new SocialObject($this->config, $this->db);
            $response = $reference->defineTwitterUser($this->data->user);

            if (!$response) {
                $error = new \SocialHelper\Error\Error(41);
                return $error;
            }

            if (is_error($response)) {
                return $response;
            }

            $reference = save_object($reference);

            if (!$reference) {
                $error = new \SocialHelper\Error\Error(42);
                return $error;
            }

            if (is_error($reference)) {
                return $reference;
            }

            $response = $this->saveReference($reference, 'twitterUser'); 

            if (!$response) {
                $error = new \SocialHelper\Error\Error(43);
                return $error;
            }

            if (is_error($response)) {
                return $response;
            }
        }

        if (!is_array($this->meta)) {
            return true;
        }

        foreach($this->meta as $meta) {
            foreach($meta as $key => $value) {
                if ('tweetUrl' == $key) {
                    $object = new SocialObject($this->config, $this->db);

                    $response = $reference->defineLink($value);

                    if (!$response) {
                        $error = new \SocialHelper\Error\Error();
                        return $error;
                    }

                    if (is_error($response)) {
                        return $response;
                    }

                    if (null !== $this->account) {
                        $type = $this->account;
                    } else {
                        $type = null;
                    }

                    $reference = save_object($reference, $type);

                    if (!$reference) {
                        $error = new \SocialHelper\Error\Error();
                        return $error;
                    }

                    if (is_error($reference)) {
                        return $reference;
                    }

                    $response = $this->saveReference($reference, 'twitterUrl'); 

                    if (!$response) {
                        $error = new \SocialHelper\Error\Error();
                        return $error;
                    }

                    if (is_error($response)) {
                        return $response;
                    }
                }
            }
        }

        return true;
    }

    private function saveReference($reference, $reference_type)
    {
        $this->db->startTransaction();
        $date = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO referenceTypes (type, dateAdded, dateUpdated) VALUES ('" . $reference_type . "', '" . $date . "', '" . $date ."')
            ON DUPLICATE KEY UPDATE referenceTypeID = LAST_INSERT_ID(referenceTypeID), dateUpdated = '" . $date . "';
        ";

        $response = $this->db->query($query);

        if (!$response) {
            $this->db->rollback();
            $error = new \SocialHelper\Error\Error(49);
            return $error;
        }

        $query = "SET @referenceTypeID = LAST_INSERT_ID();";

        $response = $this->db->query($query);

        if (!$response) {
            $this->db->rollback();
            $error = new \SocialHelper\Error\Error(44);
            return $error;
        }

        $objectID = $this->getID();

        if (!$objectID) {
            $this->db->rollback();
            $error = new \SocialHelper\Error\Error(45);
            return $error;
        }

        if (is_error($objectID)) {
            $this->db->rollback();
            return $objectID;
        }

        $referenceID = $reference->getID();

        if (!$referenceID) {
            var_dump($reference->UID);
            var_dump($reference->type);
            var_dump($referenceID);
            // print_r($reference);
            exit;
            $this->db->rollback();
            $error = new \SocialHelper\Error\Error(46);
            return $error;
        }

        if (is_error($referenceID)) {
            $this->db->rollback();
            return $referenceID;
        }

        $query = "
            INSERT INTO objectReferences (objectID, referenceID, referenceType, dateAdded, dateUpdated) VALUES (" . $objectID . ", " . $referenceID . ", @referenceTypeID, '" . $date . "', '" . $date ."')
            ON DUPLICATE KEY UPDATE objectReferenceID = LAST_INSERT_ID(objectReferenceID), dateUpdated = '" . $date . "';
        ";

        $response = $this->db->query($query);

        if (!$response) {
            $this->db->rollback();
            $error = new \SocialHelper\Error\Error(47);
            return $error;
        }

        $this->db->commit();
        return true;
    }

    public function saveMeta()
    {
        $this->meta = array();

        if ($this->type == 'tweet' && isset($this->data->entities->urls)) {
            foreach($this->data->entities->urls as $url) {

                if (isset($url->expanded_url)) {
                    $url = $url->expanded_url;
                    $this->meta[] = array('tweetUrl' => $url);
                }
            }
        }

        $this->save_fields[] = 'meta';
        return true;
    }

    public function saveKeywords()
    {
        $this->keywords = array();

        if ($this->type == 'tweet' && isset($this->data->entities->hashtags)) {
            foreach($this->data->entities->hashtags as $hashtag) {
                if (isset($hashtag->text)) {
                    $hashtag = $hashtag->text;
                    $hashtag = strtolower($hashtag);
                    $this->keywords[] = array('tweetHashtag' => $hashtag);
                }
            }
        }

        $this->save_fields[] = 'keywords';
        return true;
    }

    public function save()
    {
        $save_fields = array_unique($this->save_fields);

        if (null === $this->type) {
            $error = new \SocialHelper\Error\Error(29);
            return $error;
        }

        $this->db->startTransaction();
        $date = date('Y-m-d H:i:s');

        // if (null === $this->ID) {
            $query = "
                INSERT INTO objectTypes (type, dateAdded, dateUpdated) VALUES ('" . $this->type . "', '" . $date . "', '" . $date ."')
                ON DUPLICATE KEY UPDATE objectTypesID = LAST_INSERT_ID(objectTypesID), dateUpdated = '" . $date . "';
            ";

            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(40);
                return $error;
            }

            $query = "
                SET @objectTypeID = LAST_INSERT_ID();
            ";

            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(40);
                return $error;
            }

            $query = "
                INSERT INTO objects (UID, objectTypeID, dateAdded, dateUpdated) VALUES('" . $this->UID . "', @objectTypeID, '" . $date . "', '" . $date ."')
                ON DUPLICATE KEY UPDATE objectID = LAST_INSERT_ID(objectID), dateUpdated = '" . $date . "';
            ";

            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(40);
                return $error;
            }

            $query = "
                SET @objectID = LAST_INSERT_ID();
            ";

            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(40);
                return $error;
            }
        // } elseif (is_numeric($this->ID)) {
        //     $query = "SET @objectID = " . $this->ID .";";

        //     $response = $this->db->query($query);

        //     if (!$response) {
        //         $this->db->rollback();
        //         $error = new \SocialHelper\Error\Error(40);
        //         return $error;
        //     }
        // } else {
        //     $this->db->rollback();
        //     $error = new \SocialHelper\Error\Error(48);
        //     return $error;
        // }

        if (in_array('trackingQuery', $save_fields)) {
            $tracking_query_id = $this->tracking_query->getID();

            if (!is_numeric($tracking_query_id)) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(37);
                return $error;
            }

            $query = "
                INSERT INTO objectTrackingQuery (objectID, trackingQueryID, dateAdded, dateUpdated)
                VALUES(@objectID, " . $tracking_query_id . ", '" . $date . "', '" . $date ."')
                ON DUPLICATE KEY UPDATE objectAccountTrackingQueryID = objectAccountTrackingQueryID, dateUpdated = '" . $date . "';
            ";

            $response = $this->db->query($query);

            if (!$response) {
                $this->db->rollback();
                $error = new \SocialHelper\Error\Error(36);
                return $error;
            }
        }

        if (in_array('keywords', $save_fields)) {
            if (null !== $this->keywords) {
                foreach($this->keywords as $array) {
                    foreach($array as $type => $keyword) {
                        $query = "
                            INSERT INTO keywordTypes (type, dateAdded, dateUpdated) VALUES ('" . $type . "', '" . $date . "', '" . $date ."')
                            ON DUPLICATE KEY UPDATE keywordTypesID = LAST_INSERT_ID(keywordTypesID), dateUpdated = '" . $date . "';
                        ";

                        $response = $this->db->query($query);

                        if (!$response) {
                            $this->db->rollback();
                            $error = new \SocialHelper\Error\Error(40);
                            return $error;
                        }

                        $query = "
                            SET @keywordTypesID = LAST_INSERT_ID();
                        ";

                        $response = $this->db->query($query);

                        if (!$response) {
                            $this->db->rollback();
                            $error = new \SocialHelper\Error\Error(40);
                            return $error;
                        }

                        $query = "
                            INSERT INTO keywords (keywordTypeID, keyword, dateAdded, dateUpdated) VALUES (@keywordTypesID, '" . $keyword . "', '" . $date . "', '" . $date ."')
                            ON DUPLICATE KEY UPDATE keywordID = LAST_INSERT_ID(keywordID), dateUpdated = '" . $date . "';
                        ";

                        $response = $this->db->query($query);

                        if (!$response) {
                            $this->db->rollback();
                            $error = new \SocialHelper\Error\Error(40);
                            return $error;
                        }

                        $query = "
                            SET @keywordID = LAST_INSERT_ID();
                        ";

                        $response = $this->db->query($query);

                        if (!$response) {
                            $this->db->rollback();
                            $error = new \SocialHelper\Error\Error(40);
                            return $error;
                        }

                        $query = "
                            INSERT INTO objectKeywords (objectID, keywordID, dateAdded, dateUpdated) VALUES (@objectID, @keywordID, '" . $date . "', '" . $date ."')
                            ON DUPLICATE KEY UPDATE objectKeywordID = objectKeywordID, dateUpdated = '" . $date . "';
                        ";

                        $response = $this->db->query($query);

                        if (!$response) {
                            $this->db->rollback();
                            $error = new \SocialHelper\Error\Error(40);
                            return $error;
                        }
                    }
                }
            } 
        }

        if (in_array('meta', $save_fields)) {
            if (null !== $this->meta) {
                foreach($this->meta as $array) {
                    foreach($array as $meta_key => $meta_value) {
                        $query = "
                            INSERT INTO objectMeta (metaKey, metaValue, objectID, dateAdded, dateUpdated) VALUES ('" . $meta_key . "', '" . $meta_value . "', @objectID, '" . $date . "', '" . $date ."')
                            ON DUPLICATE KEY UPDATE dateUpdated = '" . $date . "';
                        ";

                        $response = $this->db->query($query);

                        if (!$response) {
                            $this->db->rollback();
                            $error = new \SocialHelper\Error\Error(40);
                            return $error;
                        }
                    }
                }
            } 
        }

        if (in_array('accountAction', $save_fields)) {
            if (null !== $this->account || null !== $this->action) {
                $query = "
                    INSERT INTO actions (type, dateAdded, dateUpdated) VALUES ('" . $this->action . "', '" . $date . "', '" . $date ."')
                    ON DUPLICATE KEY UPDATE actionsID = LAST_INSERT_ID(actionsID), dateUpdated = '" . $date . "';
                ";

                $response = $this->db->query($query);

                if (!$response) {
                    $this->db->rollback();
                    $error = new \SocialHelper\Error\Error();
                    return $error;
                }

                $query = "
                    SET @actionsID = LAST_INSERT_ID();
                ";

                $response = $this->db->query($query);

                if (!$response) {
                    $this->db->rollback();
                    $error = new \SocialHelper\Error\Error();
                    return $error;
                }

                $account_id = $this->account->getID();

                if (is_error($account_id)) {
                    $this->db->rollback();
                    return $account_id;
                }

                if (!$account_id) {
                    $this->db->rollback();
                    $error = new \SocialHelper\Error\Error();
                    return $error;
                }

                $query = "
                    INSERT INTO objectAccountActions (objectID, accountID, actionID, dateAdded, dateUpdated) VALUES (@objectID, " . $account_id . ", @actionsID, '" . $date . "', '" . $date ."')
                    ON DUPLICATE KEY UPDATE objectAccountActionsID = LAST_INSERT_ID(objectAccountActionsID), dateUpdated = '" . $date . "';
                ";

                $response = $this->db->query($query);

                if (!$response) {
                    $this->db->rollback();
                    $error = new \SocialHelper\Error\Error();
                    return $error;
                }
            } 
        }

        $this->db->commit();
        $response = $this->saveReferences();

        if (!$response) {
            $error = new \SocialHelper\Error\Error(50);
            return $error;
        }

        if (is_error($response)) {
            return $response;
        }

        $response = $this->getMetaObjects();

        if (is_error($response)) {
            return $response;
        }

        if ($response) {
            $response = $this->saveMetaObjects();

            if (!$response) {
                $error = new \SocialHelper\Error\Error();
                return $error;
            }

            if (is_error($response)) {
                return $response;
            }
        }   

        return true;
    }

    public function addTrackingQuery($tracking_query)
    {
        $this->save_fields[] = 'trackingQuery';
        $this->tracking_query = $tracking_query;
        return true;
    }

    public function getMetaObjects()
    {


        return true;
    }

    public function saveMetaObjects()
    {
        return true;
    }
}
