<?php

function is_objects($objects)
{
    if (!is_array($objects)) {
        return false;
    }

    if (!isset($objects[0])) {
        return false;
    }

    return is_an_object($objects[0]);
}

function is_an_object($object)
{
    if (!is_a($object, 'SocialHelper\Object\SocialObject')) {
        return false;
    }

    return true;
}
