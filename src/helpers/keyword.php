<?php

namespace SocialHelper\Keyword;

class Keyword
{
    private $keyword;
    private $type;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->keyword = null;
    }

    public function setKeyword($keyword = null)
    {
        $this->keyword = $keyword;
        return true;
    }

    public function setType($type = null)
    {
        $this->type = $type;
        return true;
    }

    public function getTypeID()
    {
        $response = $this->db->getKeywordType($this->type);
        return $response;
    }

    public function returnAddTypeQuery()
    {
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO keywordTypes (type, dateAdded, dateUpdated)";
        $query .= "VALUES(" . $this->type . ", '" . $date . "', '" . $date ."');";
        return $query;
    }
}
