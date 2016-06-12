<?php

function save_new_objects()
{
    global $db;
    
    $log = new SocialHelper\Logs\Log(1);

    $tracking_queries = get_tracking_queries();

    if (is_error($tracking_queries)) {
        $log->error($tracking_queries);
        $log->save();
        return $tracking_queries; // Tracking query error
    }

    if (!is_tracking_queries($tracking_queries)) {
        $error = new SocialHelper\Error\Error(1);
        $log->error($error);
        $log->save();
        return false; // Returned in the wrong format
    }

    // If there are tracking queries then process them
    if ($tracking_queries) {
        foreach ($tracking_queries as $tracking_query) {
            $objects = get_objects_by_tracking_query($tracking_query);

            if (is_error($objects)) {
                $log->error($objects);
                break;
            }

            if (!is_objects($objects)) {
                $error = new SocialHelper\Error\Error(3);
                $log->error($error);
                break;
            }

            if ($objects) {
                foreach ($objects as $object) {
                    $object = save_object($object, $tracking_query);

                    if (is_error($object)) {
                        $log->error($object);
                        break;
                    }

                    if (!$object) {
                        $error = new SocialHelper\Error\Error();
                        $log->error($error);
                        break;
                    }
                }
            }
        }
    }

    $log->save();
    return true;
}
