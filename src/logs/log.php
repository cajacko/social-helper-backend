<?php

namespace SocialHelper\Logs;

class Log
{
    private $log;

    public function __construct($id = 0)
    {
        $this->log = array(
            'logID' => $id,
        );
    }

    public function save()
    {
        $log = $this->log;
        // $log = json_encode($log);
        print_r($log);
    }

    public function error($error)
    {
        if (!isset($this->log['errors'])) {
            $this->log['errors'] = array();
        }

        $this->log['errors'][] = $error->getErrorLog();
    }
}
