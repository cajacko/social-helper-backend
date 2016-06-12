<?php

namespace SocialHelper\Link;

class Link
{
    private $source_url;
    private $resolved_url;

    public function __construct()
    {
        $this->source_url = null;
        $this->resolved_url = null;
    }

    public function setSourceUrl($link)
    {
        $this->source_url = $link;
        return true;
    }

    public function resolveUrl()
    {
        if (null === $this->source_url) {
            return false;
        }

        $url = $this->source_url;     
        $this->resolved_url = $url;
        return $url;
    }
}
