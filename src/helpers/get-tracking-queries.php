<?php

function get_tracking_queries()
{
    global $db;

    $tracking_queries = $db->getTrackingQueries();
    $array = array();

    if (!$tracking_queries) {
        return false;
    }

    if (!is_array($tracking_queries)) {
        $error = new \SocialHelper\Error\Error();
        return $error;
    }

    foreach ($tracking_queries as $tracking_query) {

        if (is_raw_tracking_query($tracking_query)) {

        }
        
        $array[] = new SocialHelper\TrackingQuery\TrackingQuery($tracking_query);
    }

    return $array;
}
