<?php

function get_tracking_queries()
{
    global $db;

    $tracking_queries = $db->getTrackingQueries();
    $array = array();

    foreach ($tracking_queries as $tracking_query) {

        if (is_raw_tracking_query($tracking_query)) {

        }
        
        $array[] = new SocialHelper\TrackingQuery\TrackingQuery($tracking_query);
    }

    return $array;
}
