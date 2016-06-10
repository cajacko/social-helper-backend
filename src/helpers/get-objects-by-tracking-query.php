<?php

function get_objects_by_tracking_query($tracking_query)
{
    if (!is_tracking_query($tracking_query)) {
        return new SocialHelper\Error\Error(2);
    }

    $type = $tracking_query->getType();
    // $query = $tracking_query->getQuery();

    if (is_error($type)) {
        return $type;
    }

    if (!$type) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    switch ($type) {
        case 'twitter':
            return get_twitter_objects_by_query($tracking_query);
        default:
            $error = new SocialHelper\Error\Error();
            return $error;
    }
}

function get_twitter_objects_by_query($tracking_query)
{
    global $twitter_connections;

    $twitter_connection = get_twitter_connection_for_query($tracking_query);

    if (is_error($twitter_connection)) {
        return $twitter_connection;
    }

    if (!is_twitter_app_connection($twitter_connection)) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    return $twitter_connection->getObjectsByQuery($tracking_query);
}
