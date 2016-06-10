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
                    $object_id = $object->getID();

                    if (is_error($object_id)) {
                        $log->error($object_id);
                        break;
                    }

                    if (!$object_id && !is_numeric($object_id)) {
                        $response = $object->saveNew();

                        if (is_error($response)) {
                            $log->error($response);
                            break;
                        }

                        if (!$response) {
                            $error = new SocialHelper\Error\Error(20);
                            $log->error($error);
                            break;
                        }

                        $response = $object->saveMeta();

                        if (is_error($response)) {
                            $log->error($response);
                            break;
                        }

                        if (!$response) {
                            $error = new SocialHelper\Error\Error(21);
                            $log->error($error);
                            break;
                        }
                    } else {
                        $response = $object->saveExisting();

                        if (is_error($response)) {
                            $log->error($response);
                            break;
                        }

                        if (!$response) {
                            $error = new SocialHelper\Error\Error(23);
                            $log->error($error);
                            break;
                        }
                    }

                    $response = $object->addTrackingQuery($tracking_query);

                    if (is_error($response)) {
                        $log->error($response);
                        break;
                    }

                    if (!$response) {
                        $error = new SocialHelper\Error\Error(24);
                        $log->error($error);
                        break;
                    }

                    $response = $object->save();

                    if (is_error($response)) {
                        $log->error($response);
                        break;
                    }

                    if (!$response) {
                        $error = new SocialHelper\Error\Error(25);
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
