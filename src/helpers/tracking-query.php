<?php

namespace SocialHelper\TrackingQuery;

class TrackingQuery
{
    private $type;
    private $query;
    private $ID;

    public function __construct($tracking_query = null)
    {
        $this->setType($tracking_query);
        $this->setQuery($tracking_query);
        $this->setID($tracking_query);
    }

    private function setID($tracking_query)
    {
        if (isset($tracking_query['ID'])) {
            $this->ID = $tracking_query['ID'];
        } else {
            $this->ID = null;
        }
    }

    private function setType($tracking_query)
    {
        if (isset($tracking_query['type'])) {
            $this->type = $tracking_query['type'];
        } else {
            $this->type = null;
        }
    }

    private function setQuery($tracking_query)
    {
        if (isset($tracking_query['query'])) {
            $this->query = $tracking_query['query'];
        } else {
            $this->query = null;
        }
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
