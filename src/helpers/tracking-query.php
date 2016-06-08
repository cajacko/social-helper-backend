<?php

namespace SocialHelper\TrackingQuery;

class TrackingQuery
{
    private $type;
    private $query;

    public function __construct($tracking_query = null)
    {
        if ($tracking_query && is_array($tracking_query)) {
            $this->setType($tracking_query);
            $this->setQuery($tracking_query);
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

    public function getType()
    {
        return $this->type;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
