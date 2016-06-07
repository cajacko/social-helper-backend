<?php

function get_objects_by_tracking_query($tracking_query)
{
    if (!is_tracking_query($tracking_query)) {
        return new SocialHelper\Error\Error(2);
    }

    
}
