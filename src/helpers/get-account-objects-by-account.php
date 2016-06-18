<?php

function get_account_objects_by_account($account)
{
    if (!is_account($account)) {
        return new SocialHelper\Error\Error();
    }

    $type = $account->getType();

    if (is_error($type)) {
        return $type;
    }

    if (!$type) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    switch ($type) {
        case 'twitter':
            return get_twitter_objects_by_account($account);
        default:
            $error = new SocialHelper\Error\Error();
            return $error;
    }
}

function get_twitter_objects_by_account($account)
{
    global $twitter_connections;

    $twitter_connection = get_twitter_connection_for_account($account);

    if (is_error($twitter_connection)) {
        return $twitter_connection;
    }

    if (!is_twitter_app_connection($twitter_connection)) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    return $twitter_connection->getObjectsByAccount($account);
}
