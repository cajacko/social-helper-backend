<?php

function save_object($object, $tracking_query = false)
{
    $response = $object->saveKeywords();

    if (is_error($response)) {
        return $response;
    }

    if (!$response) {
        $error = new SocialHelper\Error\Error(21);
        return $error;
    }

    $response = $object->saveMeta();

    if (is_error($response)) {
        return $response;
    }

    if (!$response) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    if ($tracking_query) {
        $response = $object->addTrackingQuery($tracking_query);

        if (is_error($response)) {
            return $response;
        }

        if (!$response) {
            $error = new SocialHelper\Error\Error(24);
            return $error;
        }
    }

    $response = $object->save();

    if (is_error($response)) {
        return $response;
    }

    if (!$response) {
        $error = new SocialHelper\Error\Error(25);
        return $error;
    }

    return $object;
}
