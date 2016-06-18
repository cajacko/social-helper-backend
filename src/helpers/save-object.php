<?php

function save_object($object, $type = false)
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

    if (is_tracking_query($type)) {
        $response = $object->addTrackingQuery($type);

        if (is_error($response)) {
            return $response;
        }

        if (!$response) {
            $error = new SocialHelper\Error\Error(24);
            return $error;
        }
    }

    if (is_account($type)) {
        $response = $object->addAccountAction($type);

        if (is_error($response)) {
            return $response;
        }

        if (!$response) {
            $error = new SocialHelper\Error\Error();
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
