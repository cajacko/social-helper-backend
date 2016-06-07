<?php

function is_tracking_queries($tracking_queries)
{
    if (!$tracking_queries) {
        return true;
    }

    if (!isset($tracking_queries[0])) {
        return false;
    }

    if (!isset($tracking_queries[0]['id'])) {
        return false;
    }

    if (!is_numeric(isset($tracking_queries[0]['id']))) {
        return false;
    }

    if (!isset($tracking_queries[0]['type'])) {
        return false;
    }

    if (!isset($tracking_queries[0]['query'])) {
        return false;
    }

    return true;
}
