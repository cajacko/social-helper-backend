<?php

namespace SocialHelper\Error;

class Error
{
    private $id;
    private $title;
    private $group;
    private $message;

    public function __construct($id = 0)
    {
        $this->setID($id);
        $this->getError();
        print_r($this->getErrorLog());
    }

    private function setID($id)
    {
        if (!is_numeric($id)) {
            $id = 0;
        }

        $this->id = $id;
    }

    private function getError()
    {
        $errors = file_get_contents('src/errors/errors.json', true);
        $errors = json_decode($errors);
        $id = $this->id;

        if (!isset($errors->$id)) {
            $id = 0;
            $this->setID($id);
        }

        $error = $errors->$id;

        $this->title = $error->title;
        $this->group = $error->group;
        $this->message = $error->message;
    }

    public function getErrorLog()
    {
        $error = array(
            'error' => true,
            'id' => $this->id,
            'title' => $this->title,
            'group' => $this->group,
            'message' => $this->message,
        );

        return $error;
    }
}
