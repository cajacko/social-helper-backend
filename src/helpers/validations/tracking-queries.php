<?php

function is_tracking_queries($tracking_queries)
{
    if (!$tracking_queries) {
        return true;
    }

    if (!isset($tracking_queries[0])) {
        return false;
    }

    return is_tracking_query($tracking_queries[0]);
}

function is_tracking_query($tracking_query)
{
    if (!is_a($tracking_query, 'SocialHelper\TrackingQuery\TrackingQuery')) {
        return false;
    }

    return true;
}

function is_raw_tracking_query($tracking_query)
{
    if (!isset($tracking_query['ID'])) {
        return false;
    }

    if (!is_numeric($tracking_query['ID'])) {
        return false;
    }

    if (!isset($tracking_query['type'])) {
        return false;
    }

    if (!isset($tracking_query['query'])) {
        return false;
    }
}
